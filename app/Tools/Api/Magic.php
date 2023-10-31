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
        $result = $this->list($table, $query ?: []);
        return send($response, 'ok', $result['data'], $result['meta']);
    }

    #[Route(methods: 'POST', pattern: '')]
    public function listMany(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody() ?: [];
        $data = [];
        foreach ($params as $vo) {
            if (!$vo['_table']) {
                continue;
            }
            $result = $this->list($vo['_table'], $vo ?: []);
            $data[] = [
                'table' => $vo['_table'],
                'data' => $result['data'],
                'meta' => $result['meta']
            ];
        }
        return send($response, 'ok', $data);
    }
    #[Route(methods: 'GET', pattern: '/config')]
    public function config(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $table = $query['table'];
        $info = ToolsMagic::query()->where('name', $table)->where('external', 0)->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }

        $data = [
            'label' => $info->label,
            'type' => $info->type,
            'fields' => $info->fields
        ];

        return send($response, 'ok', $data);
    }

    #[Route(methods: 'GET', pattern: '/{name}/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $name = $args['name'];
        $magicInfo = ToolsMagic::query()->where('name', $name)->where('external', 0)->first();
        if (!$magicInfo) {
            throw new ExceptionNotFound();
        }
        $info = ToolsMagicData::query()->where('magic_id', $args['id'])->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        $array = [
            'id' => (int)$info->id,
            ...$info->data,
        ];
        return send($response, 'ok', $array);
    }


    private function list(string $table, array $params = []): array
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

        \App\Tools\Service\Magic::queryMany($query, $fields, $params);

        $data = match ($info->type) {
            'tree' => $query->get()->toTree(),
            'pages' => $query->paginate(perPage: $limit, page: $page),
            default => $query->get(),
        };

        return format_data($data, function ($item) use ($info) {
            return $this->cast($item, $info->type);
        });
    }

    private function cast($item, $type): array
    {
        $array = [
            'id' => (int)$item->id,
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