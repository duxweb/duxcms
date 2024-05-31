<?php

namespace App\Tools\Service;

use App\Tools\Event\SourceEvent;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicGroup;
use App\Tools\Models\ToolsMagicData;
use App\Tools\Models\ToolsMagicSource;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;
use Dux\Bootstrap;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Validator;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

class Magic
{

    public static function keyMenu(): string
    {
        return 'magic.menus';
    }

    public static function getMenu(?Bootstrap $bootstrap): array
    {
        $key = self::keyMenu();
        $cache = $bootstrap?->cache ?: App::cache();

        if ($cache->has($key)) {
            return json_decode($cache->get($key), true);
        }
        try {
            $connect = App::db()->getConnection();
            if ($connect->getSchemaBuilder()->hasTable('magic') && $connect->getSchemaBuilder()->hasTable('magic_group')) {
                $groupData = ToolsMagicGroup::query()->with(['magics'])->get()->toTree();
                $data = self::menuLoopTree($groupData);
                $cache->set($key, json_encode($data));
            }
        } catch (\Exception $e) {
        }

        return $data ?: [];
    }

    /**
     * 循环处理菜单
     * @param mixed $list
     * @return array
     */
    private static function menuLoopTree(mixed $list): array
    {
        $datas = [];
        foreach ($list as $item) {
            $children = $item->magics->filter(function ($item) {
                if ($item->inline) {
                    return false;
                }
                return true;
            })->map(function ($item) {
                return [
                    'name' => $item->name,
                    'label' => $item->label
                ];
            })->values()->toArray();


            $data = [
                'name' => $item->name,
                'label' => $item->label,
                'icon' => $item->icon,
                'sort' => $item->sort,
            ];

            if ($item->children) {
                $data['children'] = [...self::menuLoopTree($item->children), ...$children];
            }

            if ($item->res) {
                $data['res'] = $item->res;
            }

            $datas[] = $data;
        }
        return $datas;
    }

    /**
     * 清空菜单缓存
     * @return void
     * @throws \Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function clean(): void
    {
        App::cache()->delete(self::keyMenu());
    }

    /**
     * 获取数据源
     * @return array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function source(): array
    {
        $source = new SourceEvent();
        // NOTE tools.magic.source
        App::event()->dispatch($source, 'tools.magic.source');
        return $source->data;
    }
    /**
     * 条件查询处理
     * @param Builder $query
     * @param array $fields
     * @param array $params
     * @return void
     */
    public static function queryMany(Builder $query, array $fields = [], array $params = []): void
    {
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
            if (!in_array($key, $fields) || $vo == '') {
                continue;
            }
            $query->where("data->$key", $vo);
        }
    }


    /**
     * 验证和格式化数据
     * @param array $fields
     * @param array $data
     * @return array
     */
    public static function formatData(array $fields, array $data): array
    {
        $data = Validator::parser($data, Validator::rule($fields));
        $sources = \App\Tools\Service\Magic::source();
        $saveData = [];
        foreach ($fields as $item) {
            $value = $data[$item['name']];
            if ($item['setting']['source']) {
                $format = $sources[$item['setting']['source']]['format'];
                if ($format) {
                    $value = $format($value);
                }
            }
            $saveData[$item['name']] = $value;
        }
        return $saveData;
    }


    /**
     * 格式化配置
     * @param array $fields
     * @return array
     */
    public static function formatConfig(array $fields): array
    {
        return array_map(function ($item) {
            $setting = $item['setting'];
            if ($setting['rules']) {
                $setting['rules'] = json_decode($setting['rules'], true);
            }
            $item['setting'] = $setting;
            if ($item['child'] && is_array($item['child'])) {
                $item['child'] = self::formatConfig($item['child']);
            }
            return $item;
        }, $fields);
    }



}