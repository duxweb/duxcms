<?php

namespace App\Content\Service;

use App\Content\Models\ArticleSource;
use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionNotFound;
use Illuminate\Support\Collection;

class Source
{
    public static function autoSave(?string $name = ''): void
    {
        $name = trim($name);
        if (!$name) {
            return;
        }
        if (!ArticleSource::query()->where('name', $name)->exists()) {
            ArticleSource::query()->create([
                'name' => $name
            ]);
        }
    }

}