<?php

declare(strict_types=1);

namespace Dux\Resources\Action;

use Dux\App;
use Dux\Resources\ResourcesEvent;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

abstract class Resources
{

    protected string $key = "id";
    protected string $label = "";
    protected string $model;
    protected bool $tree = false;
    protected array $pagination = [
        'status' => true,
        'pageSize' => 10,
    ];
    protected string $method = '';

    /**
     * 多条数据允许字段
     * @var array
     */
    public array $includesMany = [];

    /**
     * 多条数据排除字段
     * @var array
     */
    public array $excludesMany = [];

    /**
     * 单条数据允许字段
     * @var array
     */
    public array $includesOne = [];

    /**
     * 单条数据排除字段
     * @var array
     */
    public array $excludesOne = [];


    /**
     * @var callable[]
     */
    public array $createHook = [];

    /**
     * @var callable[]
     */
    public array $editHook = [];

    /**
     * @var callable[]
     */
    public array $storeHook = [];

    /**
     * @var callable[]
     */
    public array $delHook = [];

    /**
     * @var array
     */
    public array $trashedHook = [];
    /**
     * @var array
     */
    public array $restoreHook = [];
    private ResourcesEvent $event;


    use Many, One, Create, Edit, Store, Delete, DeleteMany, Trash, Restore;


    public function __construct()
    {
        $this->event = new ResourcesEvent();
        App::event()->dispatch($this->event, 'resources.' . static::class);
    }

    /**
     * 初始化
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return void
     */
    public function init(ServerRequestInterface $request, ResponseInterface $response, array $args): void
    {
    }

    /**
     * 数据转换
     * 转换数据字段内容
     * @param object $item
     * @return array
     */
    public function transform(object $item): array
    {
        return [];
    }


    /**
     * 单条或多条数据查询
     * @param Builder $query
     * @return void
     */
    public function query(Builder $query)
    {
    }

    /**
     * 多条数据查询
     * @param Builder $query
     * @param array $args
     * @param ServerRequestInterface $request
     * @return void
     */
    public function queryMany(Builder $query, ServerRequestInterface $request, array $args)
    {
    }

    /**
     * 单条数据查询
     * @param Builder $query
     * @param ServerRequestInterface $request
     * @param array $args
     * @return void
     */
    public function queryOne(Builder $query, ServerRequestInterface $request, array $args)
    {
    }

    /**
     * 多条元数据
     * @param object|array $query
     * @param array $data
     * @param ServerRequestInterface $request
     * @param array $args
     * @return array
     */
    public function metaMany(object|array $query, array $data, ServerRequestInterface $request, array $args): array
    {
        return [];
    }

    /**
     * 单条元数据
     * @param mixed $data
     * @param ServerRequestInterface $request
     * @param array $args
     * @return array
     */
    public function metaOne(mixed $data, ServerRequestInterface $request, array $args): array
    {
        return [];
    }

    /**
     * 数据保存验证
     * @param array $data
     * @param ServerRequestInterface $request
     * @param array $args
     * @return array
     */
    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [];
    }

    /**
     * 数据入库格式化
     * @param Data $data
     * @param ServerRequestInterface $request
     * @param array $args
     * @return array
     */
    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return $data->toArray();
    }

    /**
     * @param array $rule
     * @param Data $data
     * @return array
     */
    public function formatData(array $rule, Data $data): array
    {
        $datas = [];
        foreach ($rule as $key => $item) {
            $datas[$key] = is_callable($item) ? $item($data[$key], $data) : $item;
        }
        return $datas;
    }


    /**
     * @param Collection|LengthAwarePaginator|Model|null $data
     * @param callable $callback
     * @return array
     */
    public function transformData(Collection|LengthAwarePaginator|Model|null $data, callable $callback): array
    {
        return format_data($data, $callback);
    }

    public function translation(ServerRequestInterface $request, string $action): string
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $app = $route->getArgument("app");
        $name = $route->getName();
        $lastPos = strrpos($name, '.');
        $name = $lastPos !== false ? substr($name, 0, $lastPos) : $name;
        return __("message.$action", "common");
    }

    public function getSorts(array $params): array
    {
        $data = [];
        foreach ($params as $key => $vo) {
            if (!str_ends_with($key, "_sort")) {
                continue;
            }
            if ($vo != 'asc' && $vo != 'desc') {
                continue;
            }
            $field = substr($key, 0, -5);
            $data[$field] = $vo;
        }
        return $data;
    }

    public function filterData(array $includes, array $excludes, array $data): array
    {
        $array = [];
        foreach ($data as $k => $v) {
            $item = collect($v)->only($includes ?: null)->except($excludes ?: null)->all();
            if ($item['children'] && is_array($item['children'])) {
                $item['children'] = $this->filterData($includes, $excludes, $item['children']);
            }
            $array[] = $item;
        }
        return $array;
    }

}