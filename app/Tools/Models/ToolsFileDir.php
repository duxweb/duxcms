<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;

#[AutoMigrate]
class ToolsFileDir extends \Dux\Database\Model
{
	public $table = 'tools_file_dir';

    public $timestamps = false;

	public function migration(\Illuminate\Database\Schema\Blueprint $table)
	{
		$table->id();
        $table->char('name');
        $table->char('has_type');
	}
}
