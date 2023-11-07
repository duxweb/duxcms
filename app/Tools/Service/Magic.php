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
     * 提取源数据
     * @param Collection $fields
     * @param Collection $data
     * @return Collection
     */
    public static function mergeData(Collection $fields, Collection $data): Collection
    {
        $optionsData = [];
        $sourceFields = $fields->filter(function ($field) use (&$optionsData) {
            if (!$field['setting']['source'] && $field['setting']['options']) {
                $options = is_array($field['setting']['options']) ? $field['setting']['options'] : json_decode((string)$field['setting']['options'], true);
                $optionsData[$field['name']][] = $options;
                return false;
            }
            if (!$field['setting']['source']) {
                return false;
            }
            return true;
        })->values();

        $sourceIdMaps = [];
        $data->map(function ($item) use (&$sourceIdMaps, $sourceFields) {
            $sourceFields->map(function ($field) use ($item, &$sourceIdMaps) {
                $name = $field['name'];
                if (!isset($sourceIdMaps[$name])) {
                    $sourceIdMaps[$name] = [];
                }
                if (is_array($item->data[$name])) {
                    $sourceIdMaps[$name] = [...$sourceIdMaps[$name], $item->data[$name]];
                } else {
                    $sourceIdMaps[$name][] = $item->data[$name];
                }
            });
        });

        $sources = \App\Tools\Service\Magic::source();
        $sourceData = [];
        foreach ($sourceIdMaps as $field => $ids) {
            $fieldInfo = $sourceFields->where('name', $field)->first();
            $source = $sources[$fieldInfo['setting']['source']];
            $sourceData[$field] = $source['data'](ids: $ids);
        }

        return $data->map(function ($item) use ($sourceData, $fields) {
            $arr = $item->data;
            foreach ($item->data as $key => $value) {
                if (isset($optionsData[$key])) {
                    $options = collect($optionsData[$key]);
                    if (is_array($value)) {
                        $arr[$key . '_data'] = $options->whereIn('value', $value)->toArray();
                    } else {
                        $arr[$key . '_data'] = $options->where('value', $value)->first();
                    }
                }
                if (isset($sourceData[$key])) {
                    $source = collect($sourceData[$key]);
                    if (is_array($value)) {
                        $arr[$key . '_data'] = $source->whereIn('id', $value)->toArray();
                    } else {
                        $arr[$key . '_data'] = $source->where('id', $value)->first();
                    }
                }
            }
            $item->data = self::formatLabel($arr, $fields);
            return $item;
        });
    }


    private static function formatLabel(array $itemData, Collection $fields): array
    {
        foreach ($itemData as $key => $value) {
            if (!isset($itemData[$key . '_data'])) {
                continue;
            }
            if (!is_array($value)) {
                $value = [$value];
                $data = collect([$itemData[$key . '_data']]);
            }else {
                $data = collect($itemData[$key . '_data']);
            }

            $field = $fields->where('name', $key)->first();

            $label = [];
            foreach ($value as $vo) {
                $info = $data->where($field['setting']['keys_value'] ?: 'value', $vo)->first();
                if ($info) {
                    $label[] = $info[$field['setting']['keys_label'] ?: 'label'];
                }
            }
            $itemData[$key . '_label'] = implode(',', $label);
        }
        return $itemData;
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