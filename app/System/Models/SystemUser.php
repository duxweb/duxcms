<?php
declare(strict_types = 1);

namespace App\System\Models;

use Dux\App;
use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Connection;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[AutoMigrate]
class SystemUser extends Model {

    public $table = "system_user";

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('username')->unique();
        $table->string('nickname');
        $table->string('password');
        $table->string('avatar')->nullable();
        $table->boolean('status')->default(true);
        $table->timestamps();
    }

    public function seed(Connection $db) {
        $db->table($this->table)->insert([
            'username' => 'admin',
            'nickname' => 'Admin',
            'password' => password_hash('admin', PASSWORD_DEFAULT),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(SystemRole::class, 'system_user_role', 'user_id', 'role_id');
    }


    public function operates(): \Illuminate\Database\Eloquent\Relations\MorphMany {
        return $this->morphMany(LogOperate::class, 'user');
    }

    public function getPermissionAttribute(): array {
        $data = [];
        foreach ($this->roles as $item) {
            if (!$item->permission) {
                continue;
            }
            $data = [...$data, ...$item->permission];
        }
        return $data;
    }

}