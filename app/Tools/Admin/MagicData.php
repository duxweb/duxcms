<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/data',  name: 'tools.data')]
class MagicData extends Resources
{
    protected string $model = ToolsMagicData::class;

    private ?object $info = null;

    public function init(ServerRequestInterface $request, ResponseInterface $response, array $args): void
    {
        $params = $request->getQueryParams();
        $this->info = ToolsMagic::query()->where('name', $params['magic'])->first();
        $this->label = $this->info->label;

        if ($this->info->type == 'common') {
            $this->pagination = [
              'status' => false
            ];
        }
        if ($this->info->type == 'pages') {
            $this->pagination = [
                'status' => true,
                'pageSize' => 20,
            ];
        }
        if ($this->info->type == 'tree') {
            $this->tree = true;
            $this->pagination = [
                'status' => false
            ];
        }
    }

    public function query(Builder $query): void
    {
        $query->where('magic_id', $this->info->id);
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams() ?: [];
        $fields = [];
        foreach ($this->info->fields as $field) {
            $fields[] = $field['name'];
        }
        \App\Tools\Service\Magic::queryMany($query, $fields, $params);
    }

    public function transform(object $item): array
    {

        $data = [
            "id" => $item->id,
            ...\App\Tools\Service\Magic::listTransform($this->info->id, $item->data, $this->info->fields)
        ];
        if ($this->info->type == 'tree') {
            $data['parent_id'] = $item->parent_id;

            $data['children'] = $item->children ? $item->children->map(function ($vo) {
                return $this->transform($vo);
            }) : [];
        }
        return $data;
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return Validator::rule($this->info->fields ?: []);
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {

        $arr = $data->toArray();
        if ($this->info->type == 'tree') {
            unset($arr['parent_id']);
        }
        return [
            "magic_id" => $this->info->id,
            "parent_id" => $data->parent_id,
            "data" => $arr,
        ];
    }


}
