<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class Config extends Model {

    public $table = "config";

    public function migration(Blueprint $table)
    {
        $table->string("name")->comment("配置名称");
        $table->longText("value")->comment("配置值")->nullable();
    }

}