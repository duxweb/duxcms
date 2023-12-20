<?php

declare(strict_types=1);

namespace App\Member\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;

#[AutoMigrate]
class MemberLevel extends \Dux\Database\Model
{
	public $table = 'member_level';

	public function migration(\Illuminate\Database\Schema\Blueprint $table)
	{
		$table->id();
        $table->string('name')->comment('等级名称');
        $table->integer('growth')->default(0)->comment('成长值');
        $table->tinyInteger('type')->default(0)->comment('类型 0普通 1招募');
        $table->tinyInteger('default')->default(0)->comment('默认等级');
		$table->timestamps();
	}

    public function seed(Connection $db) {
        $db->table($this->table)->insert([
            'name' => '默认等级',
            'default' => 1,
        ]);
    }
}