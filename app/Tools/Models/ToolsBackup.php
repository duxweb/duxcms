<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsBackup extends \Dux\Database\Model
{
    public $table = 'tools_backup';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string("name")->comment('文件名称');
        $table->string('url')->comment('下载链接');
        $table->timestamps();
    }


    public function seed(Connection $db)
    {
    }
}
