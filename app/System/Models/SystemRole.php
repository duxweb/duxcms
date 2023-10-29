<?php

namespace App\System\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[AutoMigrate]
class SystemRole extends Model {

    public $table = "system_role";

    public function migration(Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->json('permission')->nullable();
        $table->timestamps();
    }

    protected $casts = [
        'permission' => 'array'
    ];

    public function seed(Connection $db) {
        $db->table($this->table)->insert([
            'name' => 'Admins',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(SystemUser::class, 'system_user_role', 'role_id', 'user_id');
    }

}