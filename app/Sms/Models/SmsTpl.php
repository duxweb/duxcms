<?php

declare(strict_types=1);

namespace App\Sms\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class SmsTpl extends Model
{
    public $table = 'sms_tpl';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('label')->comment('模板标识')->nullable();
        $table->string('name')->comment('名称');
        $table->string('method')->comment('发送方式');
        $table->tinyInteger('type')->default(0)->comment('模板类型');
        $table->string('content')->nullable()->comment('模板内容');
        $table->string('tpl')->nullable()->comment('模板id');
        $table->json('params')->nullable()->comment('模板变量');
        $table->timestamps();
    }


    protected $casts = ['params' => 'array'];

}
