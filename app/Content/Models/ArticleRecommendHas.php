<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class ArticleRecommendHas extends \Dux\Database\Model
{
    public $table = 'article_recommend_has';

    public $timestamps = false;

    public function migration(Blueprint $table): void
    {
        $table->bigInteger('article_id')->index();
        $table->bigInteger('recommend_id')->index();
        $table->bigInteger('sort')->index();
    }

}
