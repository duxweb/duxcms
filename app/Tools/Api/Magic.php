<?php

namespace App\Tools\Api;

use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use App\Tools\Service\Source;
use Dux\Handlers\ExceptionNotFound;
use Dux\Resources\Attribute\Action;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'api', pattern: '/tools/magic')]
class Magic
{

    #[Route(methods: 'POST', pattern: '')]
    public function listMany(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody() ?: [];
        $data = [];
        foreach ($params as $vo) {
            if (!$vo['_table']) {
                continue;
            }
            $page = $vo['_page'] ?: 1;
            $limit = $vo['_limit'];

            $result = $this->list($vo['_table'], (int)$page, (int)$limit,$vo ?: []);
            $data[] = [
                'table' => $vo['_table'],
                'data' => $result['data'],
                'meta' => $result['meta']
            ];
        }
        return send($response, 'ok', $data);
    }

    #[Route(methods: 'GET', pattern: '/{name}')]
    public function listOne(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $page = $query['page'] ?: 1;
        $limit = $query['limit'] ?? 0;
        $table = $args['name'];
        if (!$table) {
            throw new ExceptionNotFound();
        }
        $result = $this->list($table, (int)$page, (int)$limit, $query ?: []);
        return send($response, 'ok', $result['data'], $result['meta']);
    }

    #[Route(methods: 'GET', pattern: '/config')]
    public function config(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $table = $query['table'];
        $info = ToolsMagic::query()->where('name', $table)->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        if (!in_array('create', $info->external) && !in_array('edit', $info->external)) {
            throw new ExceptionNotFound();
        }
        $data = [
            'label' => $info->label,
            'type' => $info->type,
            'fields' => \App\Tools\Service\Magic::formatConfig($info->fields)
        ];
        return send($response, 'ok', $data);
    }

    #[Route(methods: 'GET', pattern: '/source')]
    public function sourceData(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $name = $params['name'];
        $keyword = $params['keyword'];
        $ids = $params['ids'] ? explode(',', $params['ids']) : null;
        $list = Source::getSourceData($name, $ids, $keyword);
        return send($response, 'ok', $list);
    }



    #[Route(methods: 'POST', pattern: '/{name}')]
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $name = $args['name'];
        $magicInfo = ToolsMagic::query()->where('name', $name)->first();
        if (!$magicInfo) {
            throw new ExceptionNotFound();
        }
        if (!in_array('create', $magicInfo->external)) {
            throw new ExceptionNotFound();
        }

        $fields = $magicInfo->fields->filter(function ($item) {
            if ($item['external'] == 'write' || $item['external'] == 'readWrite') {
                return true;
            }
            return false;
        });

        $data = \App\Tools\Service\Magic::formatData($fields, $params);

        $saveData = [
            'magic_id' => $magicInfo->id,
            'data' => $data,
        ];
        if ($magicInfo->type == 'tree') {
            $saveData['parent_id'] = $params['parent_id'];
        }
        ToolsMagicData::query()->create($saveData);
        return send($response, 'ok');
    }

    #[Route(methods: 'PUT', pattern: '/{name}/{id}')]
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $name = $args['name'];
        $magicInfo = ToolsMagic::query()->where('name', $name)->first();
        if (!$magicInfo) {
            throw new ExceptionNotFound();
        }
        if (!in_array('edit', $magicInfo->external)) {
            throw new ExceptionNotFound();
        }
        $info = ToolsMagicData::query()->where('magic_id', $args['id'])->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }

        $fields = $magicInfo->fields->filter(function ($item) {
            if ($item['external'] == 'write' || $item['external'] == 'readWrite') {
                return true;
            }
            return false;
        });

        $data = \App\Tools\Service\Magic::formatData($fields, $params);

        $info->data = $data;
        if ($magicInfo->type == 'tree') {
            $info->parent_id = $params['parent_id'];
        }
        $info->save();

        return send($response, 'ok');
    }



    #[Route(methods: 'GET', pattern: '/{name}/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $query = $request->getQueryParams();
        $name = $args['name'];
        $magicInfo = ToolsMagic::query()->where('name', $name)->first();

        if (!$magicInfo) {
            throw new ExceptionNotFound();
        }
        if (!in_array('read', $magicInfo->external)) {
            throw new ExceptionNotFound();
        }

        $info = ToolsMagicData::query()->where('magic_id', $magicInfo->id)->where('id', $args['id'])->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        $fields = [];
        foreach ($magicInfo->fields as $vo) {
            if ($vo['external'] == 'read' || $vo['external'] == 'readWrite') {
                $fields[] = $vo['name'];
            }
        }

        if ($query['_show']) {
            $data = $this->getModalData([$info], $magicInfo->fields)[0];
        }else {
            $data = [
                'id' => (int)$info->id,
            ];
            if ($magicInfo->type == 'tree') {
                $data['parent_id'] = $info->parent_id;
            }
            foreach ($info->data as $key => $vo) {
                if (in_array($key, $fields)) {
                    $data[$key] = $vo;
                }
            }
        }


        return send($response, 'ok', $data);
    }

    private function list(string $table, int $page, int $limit, array $params = []): array
    {
        $info = ToolsMagic::query()->where('name', $table)->first();

        if (!$info || !in_array('read', $info->external)) {
            throw new ExceptionNotFound();
        }
        $fields = [];
        foreach ($info->fields as $vo) {
            if ($vo['external'] == 'read' || $vo['external'] == 'readWrite') {
                $fields[] = $vo['name'];
            }
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

        if (!$params['_show']) {
            return format_data($data, function ($item) use ($info, $fields) {
                return $this->cast($item, $info->type, $fields);
            });
        }

        if ($data instanceof LengthAwarePaginator) {
            $list = $data->getCollection();
            $data->setCollection($this->getModalData($list, $info->fields));
            return format_data($data, function ($item) {
                return $item;
            });
        } else {
            $data = $this->getModalData($data, $info->fields);
            return format_data($data, function ($item) {
                return $item;
            });
        }

    }

    private function getModalData(mixed $list, array $fields): Collection
    {
        $modelData = Source::getModelData($list);
        $sourceData = Source::getSourceMapsData($modelData, $fields);
        $modelData = Source::mergeSourceData($modelData, $fields, $sourceData, true);
        return collect($modelData);
    }

    private function cast($item, $type, $fields): array
    {
        $data = [];
        foreach ($item->data as $key => $vo) {
            if (!in_array($key, $fields)) {
                continue;
            }
            $data[$key] = $vo;
        }

        $array = [
            'id' => (int)$item->id,
            ...$data,
        ];
        if ($type == 'tree') {
            $array['parent_id'] = $item->parent_id;
            if ($item->chlidren) {
                $array['children'] = $this->cast($item->chlidren, $type, $fields);
            }
        }
        return $array;
    }
}