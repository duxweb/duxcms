<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class Page extends \Dux\Database\Model
{
    public $table = 'page';

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('name')->comment('页面名');
        $table->string('title')->comment('页面标题');
        $table->string('subtitle')->nullable()->comment('页面副标题');
        $table->string('image')->nullable()->comment('页面封面图');
        $table->longText('content')->nullable()->comment('页面内容');
        $table->bigInteger('virtual_view')->default(0)->comment('虚拟访问量');
        $table->bigInteger('view')->default(0)->comment('访问量');
        $table->boolean('status')->default(true)->comment('状态');
        $table->string('keywords')->comment('关键词')->nullable();
        $table->string('descriptions')->comment('描述')->nullable();
        $table->timestamps();
    }

    public function seed(Connection $db)
    {
    }
}
