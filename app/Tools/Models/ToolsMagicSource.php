<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsMagicSource extends \Dux\Database\Model
{
    public $table = 'magic_source';


    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->string("name")->comment('名称')->nullable();
        $table->string("type")->comment('类型 data数组 url远程数据源 source内部数据源')->nullable();
        $table->json("data")->comment('数据')->nullable();
        $table->timestamps();
    }

    protected $casts = [
      'data' => 'array'
    ];

    public function seed(Connection $db)
    {
    }
}
