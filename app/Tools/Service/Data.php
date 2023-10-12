<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Data
{
    private static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return ToolsMagicData::query()->where($where);
    }

    public static function list(array $where = [], int $limit = 20): Collection
    {
        return self::query($where)->limit($limit)->get()->map(function ($item) {
            return [
              'id' => $item->id,
              ...$item->data,
            ];
        });
    }

    public static function page(array $where = [], int $limit = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $data = self::query($where)->paginate($limit);
        $list = $data->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                ...$item->data,
            ];
        });
        $data->setCollection($list);
        return $data;
    }

    public static function info(int $id, array $where = []): Collection
    {
        $info = self::query($where)->find($id);
        if (!$info) {
            throw new ExceptionNotFound();
        }
        return collect([
            'id' => $info->id,
            ...$info->data,
        ]);
    }

}