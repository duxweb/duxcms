<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ArticleReplace extends \Dux\Database\Model
{
    public $table = 'article_replace';

    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->string('from')->index();
        $table->string('to')->index();
        $table->timestamps();
    }

    public function seed(Connection $db)
    {
    }
}
