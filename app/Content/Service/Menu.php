<?php

namespace App\Content\Service;

use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Menu
{
    public static function query(string $name = '', array $where = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = \App\Content\Models\MenuData::query()->where($where);
        if ($name) {
            $query->whereHas('menu', function ($query) use ($name) {
                $query->where('name', $name);
            });
        }
        return $query;
    }

    public static function lists(string $name = '', array $where = [], ?string $path = '')
    {
        return self::query($name, $where)->get()->toTree()->map(function ($item) use ($path) {
            $item->active = false;
            if ($item->url == '/') {
                if ($path == $item->url) {
                    $item->active = true;
                }
            }else {
                if (str_contains($path, $item->url)) {
                    $item->active = true;
                }
            }
            return $item;
        });
    }

}