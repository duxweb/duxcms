<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ArticleRecommend extends \Dux\Database\Model
{
    public $table = 'article_recommend';

    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->string('name')->index();
        $table->timestamps();
    }


    public function seed(Connection $db)
    {
    }

    public function articles(): BelongsToMany {
        return $this->belongsToMany(Article::class, 'article_recommend_has', 'recommend_id', 'article_id')->withPivot('sort')->orderBy('pivot_sort');
    }

}
