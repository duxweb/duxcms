<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class Article extends \Dux\Database\Model
{
    public $table = 'article';

    use ArticleTagsTrait;

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->bigInteger('class_id')->index();
        $table->string('title', 500)->comment('文章标题');
        $table->string('subtitle')->nullable()->comment('文章副标题');
        $table->json('images')->nullable()->comment('文章封面图');
        $table->longText('content')->nullable()->comment('文章内容');
        $table->string('source')->nullable()->index()->comment('文章来源');
        $table->bigInteger('virtual_view')->default(0)->comment('虚拟访问量');
        $table->bigInteger('view')->default(0)->comment('访问量');
        $table->boolean('status')->default(true)->comment('状态');
        $table->bigInteger('collect')->comment('收藏量')->default(0);
        $table->bigInteger('comment')->comment('评论量')->default(0);
        $table->bigInteger('praise')->comment('点赞')->default(0);
        $table->string('keywords')->comment('关键词')->nullable();
        $table->string('descriptions')->comment('描述')->nullable();
        $table->string('url')->comment('自定义 url')->nullable();
        $table->timestamp('push_at')->comment('发布时间')->nullable();
        $table->json('extend')->nullable()->comment('扩展数据');
        $table->timestamps();
    }

    public function migrationAfter(Connection $db)
    {
        $pre = $db->getTablePrefix();
        $db->statement("ALTER TABLE {$pre}{$this->table} ADD FULLTEXT content_fulltext(title, content) WITH PARSER ngram");
    }

    protected $casts = [
        'extend' => 'array',
        'images' => 'array',
        'push_at' => 'datetime'
    ];

    public function seed(Connection $db)
    {
    }

    public function sources(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArticleSource::class, 'name', 'source');
    }

    public function recommend(): BelongsToMany
    {
        return $this->belongsToMany(ArticleRecommend::class, 'article_recommend_has', 'article_id', 'recommend_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArticleTags::class, 'article_tags_has', 'article_id', 'tag_id');
    }

    public function attrs(): BelongsToMany
    {
        return $this->belongsToMany(ArticleAttr::class, 'article_attr_has', 'article_id', 'attr_id');
    }

    public function class(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArticleClass::class, 'id', 'class_id');
    }
}
