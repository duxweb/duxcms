<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Category
{
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\Article::query()->where($where);
    }

    public static function lists(array $where = [])
    {
        return self::query($where)->get()->toTree();
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