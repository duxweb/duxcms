<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Menu
{
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\MenuData::query()->where($where);
    }

    public static function lists(array $where = [])
    {
        return self::query($where)->get()->toTree();
    }

}