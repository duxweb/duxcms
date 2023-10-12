<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class LogVisitSpider extends Model {

    public $table = "log_visit_spider";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->date('date')->comment("日期");
        $table->string('has_type')->comment("关联类型");
        $table->string('has_id')->comment("关联id")->nullable();
        $table->string('name')->comment("蜘蛛名称")->nullable();
        $table->string('path')->comment("页面路径")->nullable();
        $table->integer('num')->comment("数量")->default(0);
        $table->timestamps();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

}