<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class LogVisitUv extends Model {

    public $table = "log_visit_uv";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->date('date')->comment("日期");
        $table->string('has_type')->comment("关联类型");
        $table->string('has_id')->comment("关联id")->nullable();
        $table->string('uuid')->comment("唯一标识")->nullable();
        $table->string('driver')->comment("设备")->nullable();
        $table->string('ip')->comment("ip")->nullable();
        $table->string('country')->comment("国家")->nullable();
        $table->string('province')->comment("省份")->nullable();
        $table->string('city')->comment("城市")->nullable();
        $table->integer('num')->comment("数量")->default(0);
        $table->string('browser')->comment("浏览器")->nullable();
        $table->timestamps();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

}