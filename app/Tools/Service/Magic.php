<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicGroup;
use App\Tools\Models\ToolsMagicData;
use Dux\App;
use Dux\Bootstrap;
use Illuminate\Database\Eloquent\Builder;

class Magic
{

    public static function key(): string
    {
        return 'magic.menus';
    }

    public static function get(?Bootstrap $bootstrap): array
    {

        $cache = $bootstrap?->cache ?: App::cache();
        $data = $cache->get(self::key());
        if ($data) {
            return json_decode($data, true);
        }

        try {
            $connect = App::db()->getConnection();
            if ($connect->getSchemaBuilder()->hasTable('magic') && $connect->getSchemaBuilder()->hasTable('magic_group')) {
                $groupData = ToolsMagicGroup::query()->with(['magics'])->get();
                $data = [];
                foreach ($groupData as $group) {
                    $data[] = [
                        'name' => $group->name,
                        'label' => $group->label,
                        'icon' => $group->icon,
                        'children' => $group->magics->map(function ($item) {
                            return [
                                'name' => $item->name,
                                'label' => $item->label
                            ];
                        })->toArray()
                    ];
                }

                $cache->set(self::key(), json_encode($data), 2 * 60 * 60);
            }
        } catch (\Exception $e) {
        }
        return $data ?: [];
    }

    public static function clean(): void
    {
        App::cache()->delete(self::key());
    }

    public static function source(): array
    {
        $list = ToolsMagic::query()->get()->map(function ($item) {
            return [
                'label' => $item->label,
                'route' => 'tools/data?magic=' . $item->name,
                'model' => ToolsMagicData::class,
            ];
        });
        return $list->toArray();
    }

    public static function listTransform(int $magicId, array $data, array $fields): array
    {
        $sourceList = self::source();
        foreach ($fields as $field) {
            $value = $data[$field['name']];
            switch ($field['type']) {
                case 'cascader':
                case 'select':
                    $dataValue = self::fieldDataValue($magicId, $value, $field, $sourceList);
                    $data[$field['name'] . '_label'] = $dataValue[$field['setting']['keys_label'] ?: 'label'];
                    break;
                case 'cascader-multi':
                case 'select-multi':
                    $dataValue = self::fieldDataValue($magicId, $value, $field, $sourceList, true);
                    $data[$field['name'] . '_label'] = array_map(function ($item) use ($field) {
                        return $item[$field['setting']['keys_label'] ?: 'label'];
                    }, $dataValue);
                    break;
            }
        }
        return $data;
    }

    private static function fieldDataValue(int $magicId, $value, array $field, array $sources = [], bool $multi = false): array
    {
        if ($value == null) {
            return [];
        }
        if ($field['setting']['source']) {
            $source = collect($sources)->where('route', $field['setting']['source'])->first();
            if (!$source) {
                return [];
            };
            if ($source['model'] == ToolsMagicData::class) {
                $urlQuery = parse_url($source['route'])['query'];
                parse_str($urlQuery, $queryArr);
                $magic = $queryArr['magic'];
                $data = ToolsMagicData::query()->with('magic', function ($query) use($magic) {
                    $query->where('name', $magic);
                })->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        ...$item->data,
                    ];
                })->toArray();
            } else {
                $data = (new $source['model'])->query()->get()->toArray();
            }
        } else {
            $data = is_array($field['setting']['options']) ? $field['setting']['options'] : json_decode((string)$field['setting']['options'], true);
        }
        $result = [];
        foreach ($data as $vo) {
            if (!$multi && $vo[$field['setting']['keys_value']] == $value) {
                $result[] = $vo;
                break;
            }
            if ($multi) {
                if (in_array($vo[$field['setting']['keys_value'] ?: 'value'], (array)$value)) {
                    $result[] = $vo;
                }

            }
        }
        return ($multi ? $result : $result[0]) ?: [];
    }

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


}