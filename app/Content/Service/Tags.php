<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Tags
{
    // 查询
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\ArticleTags::query()->where($where);
    }

    // 列表
    public static function lists(array $where = [], int $limit = 20, $order = 'count desc')
    {
        return self::query($where)->limit($limit)->orderByRaw($order)->get();
    }

    // 分页
    public static function page(array $where = [], int $limit = 20, $order = 'count desc'): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::query($where)->orderByRaw($order)->paginate($limit);
    }

}