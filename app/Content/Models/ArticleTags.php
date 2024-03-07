<?php

declare(strict_types=1);

namespace App\Content\Models;

use App\System\Models\SystemUser;
use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ArticleTags extends \Dux\Database\Model
{
    public $table = 'article_tags';

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('name')->index();
        $table->bigInteger('count')->index();
        $table->bigInteger('view')->index();
        $table->timestamps();
    }

    public function articles(): BelongsToMany {
        return $this->belongsToMany(Article::class, 'article_tags_has', 'tag_id', 'article_id');
    }
}
