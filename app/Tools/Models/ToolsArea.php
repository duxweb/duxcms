<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsArea extends Model
{
    public $table = 'tools_area';

    public $timestamps = false;

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->char("parent_code")->default(0);
        $table->char("code")->default(0);
        $table->string("name");
        $table->integer("level");
        $table->boolean("leaf")->default(true);
    }
}
