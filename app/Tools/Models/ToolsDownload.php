<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsDownload extends \Dux\Database\Model
{
    public $table = 'tools_download';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string("user_type")->comment('关联类型')->index();
        $table->bigInteger("user_id")->comment('关联用户')->index();
        $table->string("name")->comment('文件名称');
        $table->string('url')->comment('下载链接');
        $table->timestamps();
    }


    public function seed(Connection $db)
    {
    }
}
