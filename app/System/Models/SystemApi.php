<?php

declare(strict_types=1);

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;

#[AutoMigrate]
class SystemApi extends \Dux\Database\Model
{
	public $table = 'system_api';


	public function migration(\Illuminate\Database\Schema\Blueprint $table)
	{
		$table->id();
        $table->char('name')->comment('名称');
        $table->char('secret_id')->comment('api id');
        $table->string('secret_key')->comment('api key');
        $table->boolean('status')->default(true);
		$table->timestamps();
	}
}
