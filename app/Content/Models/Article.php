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
        $table->json('images')->nullable()->comment('文章封面图');
        $table->longText('content')->nullable()->comment('文章内容');
        $table->string('source')->nullable()->index()->comment('文章来源');
        $table->bigInteger('virtual_view')->default(0)->comment('虚拟访问量');
        $table->bigInteger('view')->default(0)->comment('访问量');
        $table->boolean('status')->default(true)->comment('状态');
        $table->json('extend')->nullable()->comment('扩展数据');
        $table->timestamps();
    }

    protected $casts = [
        'extend' => 'array',
        'images' => 'array'
    ];

    public function seed(Connection $db)
    {
    }

    public function sources(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArticleSource::class, 'name', 'source');
    }

    public function class(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArticleClass::class, 'id', 'class_id');
    }
}
