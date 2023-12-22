<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Article
{
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\Article::query()->where($where);
    }

    public static function lists(array $where = [], int $limit = 20): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query($where)->limit($limit)->get();
    }

    public static function page(array $where = [], int $limit = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::query($where)->paginate($limit);
    }

    public static function info(int $id, array $where = []): object
    {
        $info = self::query($where)->find($id);
        if (!$info) {
            throw new ExceptionNotFound();
        }
        return $info;
    }

}