<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\System\Models\SystemUser;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionBusiness;
use Dux\Permission\Can;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/tools/data', name: 'tools.data', can: false)]
class MagicData extends Resources
{
    protected string $model = ToolsMagicData::class;

    private ?object $info = null;

    private string $action = '';

    public function init(ServerRequestInterface $request, ResponseInterface $response, array $args): void
    {
        $params = $request->getQueryParams();
        $this->action = $params['action'] ?: '';
        $this->info = ToolsMagic::query()->where('name', $params['magic'])->first();
        $this->label = $this->info->label;

        Can::check($request, SystemUser::class, 'tools.data.' . $this->info->group->name . '.' . $this->info->name);

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

    public function transformData(Model|LengthAwarePaginator|Collection|null $data, callable $callback): array
    {
        $fields = $this->info->fields;
        if ($data instanceof LengthAwarePaginator) {
            $list = $data->getCollection();
            if ($this->action == 'show') {
                $array = \App\Tools\Service\Magic::showData($fields, $data->pluck('data')->toArray());
                $list = $list->map(function ($item, $key) use ($array) {
                    $item->data = $array[$key];
                    return $item;
                });
            }
            $data->setCollection($list);
            return format_data($data, function ($item) {
                return $this->transform($item);
            });
        }
        if ($data instanceof Model) {
            if ($this->action == 'show') {
                $data->data = \App\Tools\Service\Magic::showData($fields, [$data->data])[0];
            }
            return format_data($data, function ($item) {
                return $this->transform($item);
            });
        }


        if ($this->action == 'show') {
            $array = \App\Tools\Service\Magic::showData($fields, $data->pluck('data')->toArray());
            $data = $data->map(function ($item, $key) use ($array) {
                $item->data = $array[$key];
                return $item;
            });
        }
        return format_data($data, function ($item) {
            return $this->transform($item);
        });
    }

    public function transform(object $item): array
    {
        $data = [
            "id" => $item->id,
            ...$item->data
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
