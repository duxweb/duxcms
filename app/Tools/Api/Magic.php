<?php

namespace App\Tools\Api;

use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'api', pattern: '/tools/magic')]
class Magic
{

    #[Route(methods: 'GET', pattern: '')]
    public function listOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $table = $query['_table'];
        if (!$table) {
            throw new ExceptionNotFound();
        }
        $data = $this->one($table, $query ?: []);
        return send($response, 'ok', $data);
    }

    #[Route(methods: 'POST', pattern: '')]
    public function listMany(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody() ?: [];
        $tables = array_keys($params);
        $data = [];
        foreach ($tables as $vo) {
            $data[$vo] = $this->one($vo, $params[$vo] ?: []);
        }
        return send($response, 'ok', $data);
    }


    private function one(string $table, array $params = []): array
    {
        $page = $params['_page'] ?: 1;
        $limit = $params['_limit'];
        $info = ToolsMagic::query()->where('name', $table)->where('external', 0)->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        $fields = [];
        foreach ($info->fields as $vo) {
            $fields[] = $vo['name'];
        }

        $query = ToolsMagicData::query();
        $query->where('magic_id', $info->id);

        if ($params['id_sort']) {
            if ($params['id_sort'] == 'desc') {
                $query->orderByDesc('id');
            }
            if ($params['id_sort'] == 'asc') {
                $query->orderBy('id');
            }
        }


        foreach ($params as $key => $vo) {
            if (str_ends_with($key, "_sort")) {
                $field = substr($key, 0, -5);
                if (!in_array($field, $fields)) {
                    continue;
                }
                if ($vo == 'desc') {
                    $query->orderByDesc("data->$field");
                }
                if ($vo == 'asc') {
                    $query->orderBy("data->$field");
                }
                continue;
            }
            if (!in_array($key, $fields)) {
                continue;
            }
            $query->where("data->$key", $vo);
        }

        $data = match ($info->type) {
            'tree' => $query->get()->toTree(),
            'pages' => $query->paginate(perPage:$limit, page: $page),
            default => $query->get(),
        };

        return format_data($data, function ($item) use ($info) {
            return $this->cast($item, $info->type);
        });
    }

    private function cast($item, $type): array
    {
        $array = [
            'id' => $item->id,
            ...$item->data,
        ];
        if ($type == 'tree') {
            $array['parent_id'] = $item->parent_id;
            if ($item->chlidren) {
                $array['children'] = $this->cast($item->chlidren, $type);
            }
        }
        return $array;
    }
}