<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Data
{
    public static function query(string $name = '', array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = ToolsMagicData::query()->where($where);
        if ($name) {
            $query->whereHas('magic', function ($query) use ($name) {
                $query->where('name', $name);
            });
        }
        return $query;
    }

    public static function lists(string $name = '', array $where = [], int $limit = 20): Collection
    {
        return self::query($name, $where)->limit($limit)->get()->map(function ($item) {
            return [
              'id' => $item->id,
              ...$item->data,
            ];
        });
    }

    public static function page(string $name = '', array $where = [], int $limit = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $data = self::query($name, $where)->paginate($limit);
        $list = $data->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                ...$item->data,
            ];
        });
        $data->setCollection($list);
        return $data;
    }

    public static function info(int $id, string $name = '', array $where = []): Collection
    {
        $info = self::query($name, $where)->find($id);
        if (!$info) {
            throw new ExceptionNotFound();
        }
        return collect([
            'id' => $info->id,
            ...$info->data,
        ]);
    }

}