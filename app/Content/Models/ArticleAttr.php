<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ArticleAttr extends Model {

    public $table = 'article_attr';

    public function migration(Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->string('name')->comment('属性');
    }

    public function seed(Connection $db) {
    }
}
