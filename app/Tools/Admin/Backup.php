<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Content\Models\Article;
use App\Tools\Event\BackupEvent;
use App\Tools\Models\ToolsBackup;
use Carbon\Carbon;
use DateTime;
use Dux\App;
use Dux\Handlers\Exception;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Utils\Excel;
use Illuminate\Database\Eloquent\Builder;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplFileObject;
use ZipArchive;
use ZipStream\ZipStream;


ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

#[Resource(app: 'admin',  route: '/tools/backup', name: 'tools.backup')]
class Backup extends Resources
{
	protected string $model = ToolsBackup::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "created_at" => $item->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args)
    {
        $query->orderByDesc('id');
    }

    public function delAfter(mixed $info): void
    {
        FileSystem::delete(base_path($info->url));
    }

    #[Action(methods: 'POST', route: '/import', name: 'import')]
    public function import(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $file = $data['file'];
        $url = $file[0]['url'];

        $client = new \GuzzleHttp\Client();
        $fileTmp = $client->request('GET', $url)->getBody()->getContents();
        $tmpFile = tempnam(sys_get_temp_dir(), 'backup_');
        $tmp = fopen($tmpFile, 'w');
        fwrite($tmp, $fileTmp);
        fclose($tmp);

        $backupTmp = base_path('data/backup_tmp');
        try {
            $zip = new ZipArchive;
            $res = $zip->open($tmpFile);
            if (!$res) {
                throw new Exception('File cannot be opened');
            }
            if (!$zip->extractTo($backupTmp)) {
                throw new Exception('Corrupted file decompression failure');
            }
            $files = Finder::findFiles('*.json')->in($backupTmp);


            $event = new BackupEvent();
            App::event()->dispatch($event,'tools.backup');
            $data = $event->get();

            $models = [];
            foreach ($files as $file) {
                $name = $file->getBasename('.json');
                if (!isset($data[$name])) {
                    continue;
                }
                $model = $data[$name]['model'];
                $model::query()->truncate();
                $models[$model] = $file->getPathname();
            }


            App::db()->getConnection()->beginTransaction();
            try {
                foreach ($models as $model => $file) {
                    $content = FileSystem::read($file);
                    $data = json_decode($content, true);
                    foreach ($data as $item) {
                        $model::query()->create($item);
                    }
                }
            }catch (\Exception $e) {
                App::db()->getConnection()->rollBack();
                throw $e;
            }
            App::db()->getConnection()->commit();
        }catch (\Exception $e) {
            throw $e;
        } finally {
            FileSystem::delete($backupTmp);
        }

        return send($response, __('tools.backup.message.import', 'manage'));
    }

    #[Action(methods: 'GET', route: '/export', name: 'export')]
    public function export(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $event = new BackupEvent();
        App::event()->dispatch($event,'tools.backup');
        $data = $event->get();

        $fileData = [];
        foreach ($data as $vo) {
            $fileData[] = [
                'label' => $vo['name'],
                'value' => $vo['name']
            ];
        }

        return send($response, 'ok', $fileData);
    }

    #[Action(methods: 'POST', route: '/export', name: 'export')]
    public function exportFile(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody() ?: [];
        $title = $body['name'];
        $tables = $body['data'] ?: [];

        if (!$tables) {
            throw new ExceptionBusiness(__('tools.backup.validator.table', 'manage'));
        }
        if (!$title) {
            throw new ExceptionBusiness(__('tools.backup.validator.title', 'manage'));
        }

        $event = new BackupEvent();
        App::event()->dispatch($event,'tools.backup');

        $data = $event->get();

        $name = date('YmdHis');
        $backupDir = base_path('data/backup');
        $backupTmp = base_path('data/backup_tmp');
        try {
            FileSystem::delete($backupTmp);
            FileSystem::createDir($backupTmp);
            FileSystem::createDir($backupDir);

            $files = [];
            foreach ($data as $vo) {
                if (!in_array($vo['name'],$tables )) {
                    continue;
                }

                $filePath = $backupTmp . '/' . $vo['name'] . '.json';
                $data = $vo['model']::all();
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);
                FileSystem::write($filePath, $json);
                $files[] = $filePath;
            }

            if (!$files) {
                throw new ExceptionBusiness(__('tools.backup.validator.notFound', 'manage'));
            }


            $zipFile = base_path('data/backup/' . $name . '.zip');
            $fileStream = fopen($zipFile, 'w+b');

            $zip = new ZipStream(
                outputStream: $fileStream,
                defaultEnableZeroHeader: false
            );

            foreach ($files as $file) {
                $relativePath = substr($file, strlen($backupTmp) + 1);
                $zip->addFileFromPath($relativePath, $file);
            }
            $zip->finish();
            fclose($fileStream);
        }catch (\Exception $e) {
            throw $e;
        } finally {
            FileSystem::delete($backupTmp);
        }

        ToolsBackup::query()->create([
            'name' => $title,
            'url' => 'data/backup/' . $name . '.zip'
        ]);

        return send($response, __('tools.backup.message.export', 'manage'));
    }

    #[Action(methods: 'POST', route: '/download/{id}', name: 'download')]
    public function download(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $info = ToolsBackup::query()->find($id);
        if (!$info) {
            throw new ExceptionBusiness('tools.backup.validator.notFound');
        }

        if (!is_file(base_path($info->url))) {
            throw new ExceptionBusiness('tools.backup.validator.notFound');
        }

        $zipFile = new SplFileObject(base_path($info->url), 'rb');
        $response = $response->withHeader('Content-Type', 'application/zip');
        $response->getBody()->write($zipFile->fread($zipFile->getSize()));
        return $response;
    }
}
