<?php

namespace App\Tools\Service;

use App\Tools\Event\SourceEvent;
use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicGroup;
use App\Tools\Models\ToolsMagicData;
use App\Tools\Models\ToolsMagicSource;
use Dux\App;
use Dux\Bootstrap;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Validator;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;

class Source
{


    public static function mergeSourceData(mixed $list, array $fields, array $sourceMaps, bool $label = false): array
    {
        foreach ($list as $i => $datum) {
            foreach ($datum as $key => $value) {
                $sourceValue = $value;
                // 获取字段
                $field = collect($fields)->first(function ($item) use ($key) {
                    return $item['name'] == $key;
                });

                if (!$field) {
                    continue;
                }

                $sourceId = $field['setting']['source'];
                if ($sourceId) {
                    if (!$sourceMaps[$sourceId]) {
                        continue;
                    }
                    $idField = $field['setting']['keys_value'];
                    if (!$idField) {
                        $idField = 'value';
                    }
                    $labelField = $field['setting']['keys_label'];

                    // 合并当前id
                    $ids = [];
                    if (is_array($value)) {
                        $ids = $value;
                    } else {
                        $ids[] = $value;
                    }

                    // 获取当前源数据
                    if ($field['type'] == 'cascader' || $field['type'] == 'cascader-multi') {
                        $filterSourceData = self::findTreeSource($sourceMaps[$sourceId], $ids, $idField);
                    }else {
                        $filterSourceData = self::findSource($sourceMaps[$sourceId], $ids, $idField);
                    }

                    // 重设值数据
                    if ($filterSourceData) {
                        if (is_array($value)) {
                            $value = $filterSourceData;
                        }else {
                            $value = $filterSourceData[0];
                        }
                    }
                }

                // 处理子节点
                if ($field['child'] && is_array($value)) {
                    $value = self::mergeSourceData($value, $field['child'], $sourceMaps, $label);
                }

                if ($label) {
                    $datum[$key] = $sourceValue;
                    $datum[$key . '_data'] = $value;

                }else {
                    $datum[$key] = $value;

                }
            }

            // 处理属性
            if ($datum["children"]) {
                $datum["children"] = self::mergeSourceData($datum["children"], $fields, $sourceMaps, $label);
            }

            $list[$i] = $datum;
        }

        return $list;
    }


    public static function findSource($sourceMaps, array $ids, string $field, ?string $label = null): array
    {
        return collect($sourceMaps)->filter(function ($item) use ($field, $ids) {
            return in_array($item[$field], $ids);
        })->map(function ($item) use ($label) {
            return $label ? $item[$label] : $item;
        })->toArray();
    }

    // 获取数据源
    public static function findTreeSource($sourceMaps, array $ids, string $field, ?string $label = null): array
    {
        $tree = new \BlueM\Tree(self::flatTree($sourceMaps), ['rootId' => null, 'id' => $field, 'parent' => 'parent_id']);
        $data = [];
        foreach ($ids as $id) {
            $ancestors = $tree->getNodeById($id)->getAncestorsAndSelf();
            $ancestorsArray = array_map(function($node) use ($label) {
                return $label ? $node->get($label) : $node->toArray();
            }, $ancestors);
            $data[] = array_reverse($ancestorsArray);
        }
        return $data;
    }

    public static function flatTree($sourceMaps): array
    {
        $result = [];
        foreach ($sourceMaps as $vo) {
            $tmp = $vo;
            unset($tmp['children']);
            $result[] = $tmp;
            if ($vo['children']) {
                $childData = self::flatTree($vo['children']);
                if ($childData) {
                    $result = [...$result, ...$childData];
                }
            }
        }
        return $result;
    }


    // 解压模型数据
    public static function getModelData(mixed $list): array
    {
        $result = [];
        foreach ($list as $item) {
            $data = $item->data;
            $data['id'] = $item->id;
            $data['parent_id'] = $item->parent_id;
            $data['created_at'] = $item->created_at?->toDateTimeString();
            $data['updated_at'] = $item->updated_at?->toDateTimeString();
            if ($item->children) {
                $data['children'] = self::GetModelData($item->children);
            }
            $result[] = $data;
        }
        return $result;
    }


    // 获取映射数据源
    public static function getSourceMapsData(mixed $data, array $fields): array
    {
        $sources = [];
        self::getSourceMapsIds($data, $fields, $sources);

        $result = [];
        foreach ($sources as $sourceId => $ids) {
            $sourceData = self::getSourceData($sourceId, $ids);
            $result[$sourceId] = $sourceData;
        }
        return  $result;

    }

    // 获取数据源->数据id映射
    public static function getSourceMapsIds(mixed $list, array $fields, array &$source): void
    {
        foreach ($list as $datum) {
            foreach ($datum as $key => $value) {
                // 获取字段
                $field = collect($fields)->first(function ($item) use ($key) {
                    return $item['name'] == $key;
                });

                if (!$field) {
                    continue;
                }
                // 处理源数据
                $sourceId = $field['setting']['source'];
                if ($sourceId) {
                    if (!$source[$sourceId]) {
                        $source[$sourceId] = [];
                    }
                    if (is_array($value)) {
                        $source[$sourceId] = [...$source[$sourceId], ...$value];
                    }else {
                        $source[$sourceId] = [...$source[$sourceId], $value];
                    }
                }

                // 处理子节点
                if ($field['child'] && is_array($value)) {
                    self::getSourceMapsIds($value ?: [], $field['child'], $source);
                }
            }

            // 处理树形
            if ($datum['children']) {
                self::getSourceMapsIds($datum['children'], $fields, $source);
            }
        }
    }

    /**
     * 获取数据源数据
     * @param string $id
     * @param array|null $ids
     * @param string|null $keyword
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSourceData(string $id, ?array $ids = [], ?string $keyword = ''): array
    {
        $info = ToolsMagicSource::query()->find($id);
        if (!$info) {
            throw new ExceptionBusiness("数据不存在");
        }
        if (!$info->type || $info->type == 'data') {
            return $info->data ?: [];
        }

        if ($info->type == 'remote') {
            $config = $info->data;
            $client = new Client();
            $result = $client->request($config['method'], $config['url'], [
                'headers' => $config['headers'],
                'query' => $config['query'],
                'form_params' => $config['form_params'],
                'json' => $config['json'],
            ]);
            $content = $result->getBody()?->getContents();
            $data = json_decode($content, true);
            $res = data_get($data, $config['fields']);
            return is_array($res) ? $res : [];
        }

        if ($info->type == 'source') {
            $sources = \App\Tools\Service\Magic::source();
            $list = [];
            $source = $sources[$info->data["name"]];
            if ($source) {
                $list = $source['data'](keyword: $keyword, ids: $ids);
            }
            return $list;
        }

        return [];
    }


}