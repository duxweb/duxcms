<?php

namespace App\Tools\Handlers;

ini_set('max_execution_time', 600);

use App\Tools\Models\ToolsFile;
use App\Tools\Models\ToolsFileDir;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Mimey\MimeTypes;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Dux\Utils\Content;

class Upload
{

    private string $hasType = '';

    public function upload(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        /**
         * @var $uploads UploadedFileInterface[]
         */
        $body = $request->getParsedBody();
        $uploads = $request->getUploadedFiles();
        $app = $request->getAttribute('app');
        $list = [];
        $mimes = new MimeTypes;
        $type = App::config("storage")->get("type");
        $ext = App::config("storage")->get("ext", []);
        foreach ($uploads as $key => $vo) {
            $content = $vo->getStream()->getContents();
            $extension = pathinfo($vo->getClientFilename(), PATHINFO_EXTENSION);
            $mime = $vo->getClientMediaType();
            if (!$extension) {
                $extension = $mimes->getExtension($mime);
            }
            if ($ext && !in_array($extension, $ext)) {
                throw new ExceptionBusiness('文件格式不支持');
            }
            $basename = bin2hex(random_bytes(10));
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $path = date('Y-m-d') . '/' . $filename;
            App::storage()->write($path, $content);
            $item = [
                'dir_id' => $body['dir_id'],
                'has_type' => $app,
                'driver' => $type,
                'url' => App::storage()->publicUrl($path),
                'path' => $path,
                'name' => $vo->getClientFilename(),
                'ext' => $extension,
                'size' => $vo->getSize(),
                'mime' => $mime,
            ];
            ToolsFile::query()->create($item);
            $list[] = [
                'url' => $item['url'],
                'name' => $item['name'],
                'ext' => $item['ext'],
                'size' => $item['size'],
                'mime' => $item['mime'],
            ];
        }
        return send($response, "ok", $list);
    }

    public function remote(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?: [];
        $imageMaps = Content::localImages([
            $data['url']
        ]);
        return send($response, "ok", $imageMaps);
    }

    public function qiniu(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $config = App::config("storage")->get('drivers.default');
        $adapter = new QiniuAdapter($config["accessKey"], $config["secretKey"], $config["bucket"], $config["domain"]);
        $token = $adapter->getAuthManager()->uploadToken($config["bucket"]);
        return send($response, 'ok', [
            'token' => $token,
            'bucket' => $config["bucket"],
            'domain' => $config["domain"],
            'public_url' => $config["public_url"],
        ]);
    }

    public function manage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->hasType = $request->getAttribute('app');
        $query = $request->getQueryParams();
        $params = $request->getParsedBody();
        $type = $query['type'];
        $id = $query['id'];
        $accept = $query['accept'];
        $keyword = $query['keyword'];
        $name = $params['name'];

        $data = [];
        if ($type == 'folder') {
            $data = $this->getFolder();
        }
        if ($type == 'files') {
            $data = $this->getFile($id, $keyword, $accept);
        }
        if ($type == 'files-delete') {
            $data = $this->deleteFile($id);
        }
        if ($type == 'folder-create') {
            $data = $this->createFolder($name);
        }
        if ($type == 'folder-delete') {
            $data = $this->deleteFolder($id);
        }

        return send($response, 'ok', ...$data);
    }

    /**
     * @return mixed
     */
    private function getFolder()
    {
        $list = ToolsFileDir::query()->where('has_type', $this->hasType)->get()->toArray();
        return [$list];
    }

    private function getFile($dirId, $keyword = '', $accept = ''): array
    {
        $query = ToolsFile::query()->where('has_type', $this->hasType);
        if ($dirId) {
            $query->where('dir_id', $dirId);
        }
        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }
        if ($accept) {
            $accept = str_replace('*', '%', $accept);
            $query->where('mime', 'like', $accept);
        }
        $data = $query->orderBy('id', 'desc')->paginate(12, [
            'id', 'dir_id', 'url', 'name', 'ext', 'size', 'mime', 'created_at'
        ]);

        return format_data($data, function ($item) {
            $item['size'] = human_filesize($item->size);
            return $item;
        });
    }

    /**
     * @param $ids
     * @return array
     */
    private function deleteFile($ids): array
    {
        $ids = array_filter(explode(',', $ids));
        if (empty($ids)) {
            trigger_error('请选择删除文件');
        }
        $files = ToolsFile::query()->where('has_type', $this->hasType)->whereIn('id', $ids)->get([
            'driver', 'path'
        ]);
        $files->map(function ($vo) {
            App::storage($vo->driver)->delete($vo->path);
        });
        ToolsFile::query()->whereIn('id', $ids)->delete();
        return [];
    }

    /**
     * @param $name
     * @return array
     */
    private function createFolder($name): array
    {
        if (empty($name)) {
            trigger_error('请输入目录名称');
        }
        $file = new ToolsFileDir();
        $file->name = $name;
        $file->has_type = $this->hasType;
        $file->save();
        return [
            [
                'id' => $file->id,
                'name' => $name,
            ]
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    private function deleteFolder(int $id): array
    {
        if (empty($id)) {
            trigger_error('请选择目录');
        }
        $files = ToolsFile::query()->where('has_type', $this->hasType)->where('dir_id', $id)->get([
            'driver', 'path'
        ]);
        $files->map(function ($vo) {
            App::storage($vo->driver)->delete($vo->path);
        });
        ToolsFile::query()->where('dir_id', $id)->delete();
        ToolsFileDir::query()->where('id', $id)->delete();
        return [];
    }
}