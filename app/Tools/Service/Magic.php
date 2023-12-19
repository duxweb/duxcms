<?php

namespace App\Tools\Service;

use App\Tools\Event\SourceEvent;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicGroup;
use App\Tools\Models\ToolsMagicData;
use Dux\App;
use Dux\Bootstrap;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

                $groupData = ToolsMagicGroup::query()->with(['magics'])->get();
                $data = [];
                foreach ($groupData as $group) {
                    $children = $group->magics->filter(function ($item) {
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

                    if (!$children) {
                        continue;
                    }
                    $data[] = [
                        'name' => $group->name,
                        'label' => $group->label,
                        'icon' => $group->icon,
                        'children' => $children
                    ];
                }
                $cache->set($key, json_encode($data));
            }
        } catch (\Exception $e) {
        }

        return $data ?: [];
    }

    public static function clean(): void
    {
        App::cache()->delete(self::keyMenu());
    }

    /**
     * 获取数据源
     * @return array
     */
    public static function source(): array
    {
        $source = new SourceEvent();
        // NOTE tools.magic.source
        App::event()->dispatch($source, 'tools.magic.source');
        return $source->data;
    }


    /**
     * 拼接展示数据
     * @param array $fields
     * @param array $data
     * @return array
     */
    public static function showData(array $fields, array $data): array
    {
        $extendData = self::extendData($fields, $data);
        foreach ($data as $key => $vo) {
            $data[$key] = self::formatItem($vo, $extendData[$key]);
        }
        return $data;
    }


    /**
     * 提取源数据
     * @param array $fields
     * @param array $data
     * @return array
     */
    public static function extendData(array $fields, array $data): array
    {
        $fields = collect($fields);
        $optionsData = [];
        $sourceFields = $fields->filter(function ($field) use (&$optionsData) {
            if (!$field['setting']['source'] && $field['setting']['options']) {
                $options = is_array($field['setting']['options']) ? $field['setting']['options'] : json_decode((string)$field['setting']['options'], true);
                $optionsData[$field['name']] = [
                    'key' => 'value',
                    'type' => $field['type'],
                    'data' => $options
                ];
                return false;
            }
            if (!$field['setting']['source']) {
                return false;
            }
            return true;
        })->values();

        $sourceIdMaps = [];
        foreach ($data as $item) {
            $sourceFields->map(function ($field) use ($item, &$sourceIdMaps) {
                $name = $field['name'];
                if (!isset($sourceIdMaps[$name])) {
                    $sourceIdMaps[$name] = [];
                }
                if (is_array($item[$name])) {
                    $sourceIdMaps[$name] = [...$sourceIdMaps[$name], $item[$name]];
                } else {
                    $sourceIdMaps[$name][] = $item[$name];
                }
            });
        }

        $sources = \App\Tools\Service\Magic::source();
        foreach ($sourceIdMaps as $field => $ids) {
            $fieldInfo = $sourceFields->where('name', $field)->first();
            $source = $sources[$fieldInfo['setting']['source']];
            $optionsData[$field] = [
                'key' => 'id',
                'type' => $fieldInfo['type'],
                'data' => $source['data'](ids: $ids)
            ];
        }



        $treeType = ['cascader', 'cascader-multi'];

        $hasData = [];
        foreach ($data as $item) {
            $arr = [];
            foreach ($item as $key => $value) {

                if (isset($optionsData[$key])) {
                    $options = $optionsData[$key];
                    $tmp = is_array($value) ? $value : [$value];

                    if (in_array($options['type'], $treeType)) {
                        $paths = [];
                        foreach ($tmp as $v) {
                            $paths[] = self::findTreePath($options['data'], $v, $options['key']);
                        }
                        if (is_array($value)) {
                            $arr[$key] = $paths;
                        }else {
                            $arr[$key] = $paths[0];
                        }
                    } else {
                        $paths = [];
                        foreach ($options['data'] as $vo) {
                            if (in_array($vo[$options['key']], $tmp)) {
                                $paths[] = $vo;
                            }
                        }
                        if (is_array($value)) {
                            $arr[$key] = $paths;
                        }else {
                            $arr[$key] = $paths[0];
                        }
                    }
                }

            }
            $hasData[] = $arr;
        }
        return $hasData;
    }


    private static function findTreePath($array, $target, $key, $path = []) {

        foreach ($array as $element) {
            $newElement = $element;
            unset($newElement['children']);

            if ($element[$key] === $target) {
                $path[] = $newElement;
                return $path;
            }

            if (isset($element['children']) && is_array($element['children'])) {
                $newPath = $path;
                $newPath[] = $newElement;
                $result = self::findTreePath($element['children'], $target, $key, $newPath);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }


    private static function formatItem(array $data, array $extendData): array
    {
        foreach ($data as $key => $value) {
            if (!$extendData[$key]) {
                continue;
            }
            $data[$key] = $extendData[$key];
        }
        return $data;
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

    public static function formatConfig(array $fields): array
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
                $item['child'] = self::formatConfig($item['child']);
            }

            return $item;
        }, $fields);
    }

}