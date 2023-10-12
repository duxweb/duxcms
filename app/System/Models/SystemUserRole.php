<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class SystemUserRole extends Model {

    public $table = "system_user_role";

    public $timestamps = false;

    public function migration(Blueprint $table)
    {
        $table->integer('role_id');
        $table->integer('user_id');
    }

    public function seed(Connection $db) {
        $db->table($this->table)->insert([
            'role_id' => 1,
            'user_id' => 1,
        ]);
    }

}