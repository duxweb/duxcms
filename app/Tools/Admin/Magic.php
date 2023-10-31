<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsArea;
use App\Tools\Models\ToolsMagic;
use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Manage\Manage;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Resources\Attribute\Action;
use Dux\Route\Attribute\Route;
use Dux\Utils\Excel;
use Dux\Validator\Data;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/magic',  name: 'tools.magic')]
class Magic extends Resources
{
    protected string $model = ToolsMagic::class;

    public array $excludesMany = ['fields'];

    public function transform(object $item): array
    {
        return [
            "group_id" => $item->group_id,
            "id" => $item->id,
            "name" => $item->name,
            "group_icon" => $item->group->icon,
            "group_label" => $item->group->label,
            "label" => $item->label,
            "type" => $item->type,
            'page' => $item->page,
            "external" => $item->external,
            "fields" => $item->fields
        ];
    }



    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();
        $name = $params['label'];
        if($name){
            $query->where('label', $name);
        }
        if ($params['group']) {
            $query->where('group', $params['group']);
        }
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {

        $fields = $data['fields'];
        if (!$fields) {
            throw new ExceptionBusiness(__('tools.magic.validator.fields', 'manage'));
        }

        foreach ($fields as $field) {
            if (!$field['name'] || !$field['label']) {
                throw new ExceptionBusiness(__('tools.magic.validator.fieldsFull', 'manage'));
            }
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $field['name'])) {
                throw new ExceptionBusiness(__('tools.magic.validator.fieldsFormat', 'manage'));
            }
        }

        return [
            "group_id" => ["required", __('tools.magic.validator.group', 'manage')],
            "name" => ['regex', '/^[a-zA-Z][a-zA-Z0-9_]*$/', __('tools.magic.validator.name', 'manage')],
            "label" => ["required", __('tools.magic.validator.label', 'manage')],

        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "group_id" => $data->group_id,
            "name" => $data->name,
            "label" => $data->label,
            'type' => $data->type,
            'page' => (int)$data->page,
            "external" => $data->external,
            "fields" => $data->fields,
        ];
    }

    public function createAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function editAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function storeAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function delAfter(mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    #[Action(methods: 'GET', route: '/source')]
    public function source(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $list = array_map(function ($item) {
            return [
                'label' => $item['label'],
                'value' => $item['name']
            ];
        }, \App\Tools\Service\Magic::source());
        return send($response, 'ok', array_values($list));
    }

    #[Action(methods: 'GET', route: '/sourceData')]
    public function sourceData(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $name = $params['name'];
        $keyword = $params['keyword'];
        $ids = $params['ids'];
        $sources = \App\Tools\Service\Magic::source();
        $list = [];
        $source = $sources[$name];
        if ($source) {
            $list = $source['data'](keyword: $keyword, ids: $ids);
        }
        return send($response, 'ok', $list);
    }

    #[Action(methods: 'GET', route: '/config')]
    public function config(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $info = ToolsMagic::query()->where('name', $params['magic'])->first();
        if (!$info) {
            throw new ExceptionBusiness(__('tools.magic.validator.data', 'manage'));
        }
        $info->fields = $this->formatConfig($info->fields);

        return send($response, 'ok', $info?->toArray());
    }

    public function formatConfig(array $fields): array
    {
        return array_map(function ($item) {
            $setting = $item['setting'];
            if ($setting['options'] && is_string($setting['options'])) {
                $setting['options'] = json_decode($setting['options'], true);
            }
            if ($setting['rules']) {
                $setting['rules'] = json_decode($setting['rules'], true);
            }
            $item['setting'] = $setting;
            if ($item['child'] && is_array($item['child'])) {
                $item['child'] = $this->formatConfig($item['child']);
            }

            return $item;
        }, $fields);
    }

}
