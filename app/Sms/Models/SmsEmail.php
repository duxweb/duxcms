<?php

declare(strict_types=1);

namespace App\Sms\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class SmsEmail extends Model
{
    public $table = 'sms_email';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('label')->comment('模板标识')->nullable();
        $table->string('name')->comment('名称');
        $table->text('content')->nullable()->comment('模板内容');
        $table->timestamps();
    }

}
