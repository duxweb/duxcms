<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class LogVisitData extends Model {

    public $table = "log_visit_data";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->date('date')->comment("日期");
        $table->string('has_type')->comment("关联类型");
        $table->string('has_id')->comment("关联id")->nullable();
        $table->string('driver')->comment("设备")->nullable();
        $table->integer('pv')->comment("访问量")->default(1);
        $table->integer('uv')->comment("访客量")->default(1);
        $table->timestamps();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

}