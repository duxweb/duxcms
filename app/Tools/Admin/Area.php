<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsArea;
use Dux\App;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Utils\Excel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

ini_set('memory_limit', '1024M');

#[Resource(app: 'admin',  route: '/tools/area',  name: 'tools.area', actions: ['list'])]
class Area extends Resources
{
    protected string $model = ToolsArea::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "code" => $item->code,
            "name" => $item->name,
            "level" => $item->level,
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {

        $params = $request->getQueryParams();
        $level = $params["level"];
        $name = $params['name'];

        if ($level) {
            $query->where('level', $level);
        }

        if($name) {
            $area = ToolsArea::query()->where('name', $name)->first();
            $query->where('parent_code', $area->code);
        }
    }

    #[Action(methods: 'POST', route: '', name: 'import')]
    public function import(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $data = $request->getParsedBody();
        $file = $data['file'];

        $data = Excel::import($file[0]['url']);
        $data = array_slice($data, 1);

        $newData = [];
        foreach ($data as $key => $vo) {
            if (!$newData[$vo[1] . ':1']) {
                $newData[$vo[1] . ':1'] = [
                    'parent_code' => 0,
                    'code' => $vo[1],
                    'name' => $vo[0],
                    'level' => 1,
                    'leaf' => true,
                ];
            }

            if (!$newData[$vo[3] . ':2']) {
                $newData[$vo[3] . ':2'] = [
                    'parent_code' => $vo[1],
                    'code' => $vo[3],
                    'name' => $vo[2],
                    'level' => 2,
                    'leaf' => true,
                ];
                $newData[$vo[1] . ':1']['leaf'] = false;
            }

            if (!$newData[$vo[5] . ':3']) {
                $newData[$vo[5] . ':3'] = [
                    'parent_code' => $vo[3],
                    'code' => $vo[5],
                    'name' => $vo[4],
                    'level' => 3,
                    'leaf' => true,
                ];
                $newData[$vo[3] . ':2']['leaf'] = false;
            }

            if (!$newData[$vo[7] . ':4']) {
                $newData[$vo[7] . ':4'] = [
                    'parent_code' => $vo[5],
                    'code' => $vo[7],
                    'name' => $vo[6],
                    'level' => 4,
                    'leaf' => true,
                ];
                $newData[$vo[5] . ':3']['leaf'] = false;
            }
        }
        $list = array_chunk(collect(array_values($newData))->sortBy('code')->toArray(), 1000);

        App::db()->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 0');
        App::db()->getConnection()->table('tools_area')->truncate();
        App::db()->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 1');
        
        App::db()->getConnection()->beginTransaction();
        try {
            foreach ($list as $vo) {
                App::db()->getConnection()->table('tools_area')->insert(array_values($vo));
            }
            App::db()->getConnection()->commit();
        } catch (Exception $e) {
            App::db()->getConnection()->rollBack();
            throw $e;
        }
        return send($response, "导入成功");
    }

    #[Action(methods: 'GET', route: '/select')]
    public function cascade(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $level = $params['level'] ?: 0;
        $parent = $params['parent'];
        $model = new ToolsArea();
        $info = $model->query()->where('name', $parent)->where('level', $level)->first();
        $data = $model->query()->where('level', $info->level + 1)->where('parent_code', $parent ? $info['code'] : 0)->get(["name as value", "name as label", "leaf"])->toArray();
        return send($response, 'ok', $data);

    }


}
