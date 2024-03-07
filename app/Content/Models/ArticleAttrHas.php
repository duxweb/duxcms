<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ArticleAttrHas extends Model {

    public $table = 'article_attr_has';

    public $timestamps = false;

    public function migration(Blueprint $table) {
        $table->bigInteger('attr_id')->index();
        $table->bigInteger('article_id')->index();
    }

    public function seed(Connection $db) {
    }
}
