<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class Article extends \Dux\Database\Model
{
    public $table = 'article';
    public function migration(Blueprint $table)
    {
        $table->id();
        $table->bigInteger('class_id')->index();
        $table->string('title')->comment('文章标题');
        $table->string('subtitle')->nullable()->comment('文章副标题');
        $table->string('image')->nullable()->comment('文章封面图');
        $table->longText('content')->nullable()->comment('文章内容');
        $table->string('author')->nullable()->comment('文章作者');
        $table->bigInteger('virtual_view')->default(0)->comment('虚拟访问量');
        $table->bigInteger('view')->default(0)->comment('访问量');
        $table->boolean('status')->default(true)->comment('状态');
        $table->timestamps();
    }


    public function seed(Connection $db)
    {
    }

    public function class(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArticleClass::class, 'id', 'class_id');
    }
}
