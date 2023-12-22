<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Page
{
    public static function query(array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Content\Models\Page::query()->where($where);
    }

    public static function info(string $name, array $where = []): object
    {
        $where['name'] = $name;
        $info = self::query($where)->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        return $info;
    }

}