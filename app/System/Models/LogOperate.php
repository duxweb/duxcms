<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class LogOperate extends Model {

    public $table = "log_operate";
    protected $casts = ['request_params' => 'array'];

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('user_type')->comment("关联类型");
        $table->string('user_id')->comment("关联id");
        $table->text('request_method')->comment("请求方法");
        $table->string('request_url')->comment("请求链接");
        $table->json('request_params')->comment("请求参数")->nullable();
        $table->decimal('request_time', 11, 5)->comment("请求时间");
        $table->string('route_name')->comment("路由名")->nullable();
        $table->string('route_title')->comment("路由标题")->nullable();
        $table->string('client_ua')->comment("客户端ua")->nullable();
        $table->string('client_ip', 50)->comment("客户端ip")->nullable();
        $table->string('client_browser')->comment("客户端浏览器")->nullable();
        $table->string('client_device')->comment("客户端设备")->nullable();
        $table->timestamps();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\MorphTo {
        return $this->morphTo();
    }

}