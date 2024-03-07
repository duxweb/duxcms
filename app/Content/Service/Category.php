<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Category
{
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\ArticleClass::query()->where($where);
    }

    public static function lists(array $where = [])
    {
        return self::query($where)->get();
    }

    public static function tree(array $where = [])
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

    public static function breadcrumb(int $id)
    {
        return \App\Content\Models\ArticleClass::query()->ancestorsAndSelf($id);
    }

}