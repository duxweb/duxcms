<?php

namespace App\System\Models;

use Dux\App;
use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Connection;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[AutoMigrate]
class LogLogin extends Model {

    public $table = "log_login";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('user_type')->comment("关联类型");
        $table->string('user_id')->comment("关联id");
        $table->string('browser')->nullable();
        $table->string('ip')->nullable();
        $table->string('platform')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamps();
    }

}