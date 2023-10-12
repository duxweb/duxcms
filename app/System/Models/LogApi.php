<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class LogApi extends Model {

    public $table = "log_api";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('has_type')->comment("关联类型");
        $table->string('method')->comment("请求方法");
        $table->string('name')->comment("路由名")->nullable();
        $table->string('title')->comment("路由标题")->nullable();
        $table->date('date')->comment("日期");
        $table->integer('pv')->comment("访问量")->default(1);
        $table->integer('uv')->comment("访客量")->default(1);
        $table->decimal('max_time', 11, 3)->comment("最大时间");
        $table->decimal('min_time', 11, 3)->comment("最小时间");
        $table->timestamps();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

}