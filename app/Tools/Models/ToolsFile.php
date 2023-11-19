<?php

declare(strict_types=1);

namespace App\Tools\Models;

use App\Content\Models\ArticleClass;
use Dux\Database\Attribute\AutoMigrate;

#[AutoMigrate]
class ToolsFile extends \Dux\Database\Model
{
	public $table = 'tools_file';


	public function migration(\Illuminate\Database\Schema\Blueprint $table)
	{
		$table->id();
        $table->bigInteger('dir_id')->nullable();
        $table->char('has_type');
        $table->char('driver');
        $table->string('url');
        $table->string('path');
        $table->string('name');
        $table->char('ext');
        $table->integer('size')->default(0);
        $table->string('mime')->nullable();
		$table->timestamps();
	}

    public function dir(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ToolsFileDir::class, 'id', 'dir_id');
    }
}
