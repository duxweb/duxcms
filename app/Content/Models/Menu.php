<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class Menu extends \Dux\Database\Model
{
	public $table = 'menu';

	public function migration(Blueprint $table)
	{
		$table->id();
        $table->string('name')->comment('调用名');
        $table->string('title')->comment('菜单描述');
		$table->timestamps();
	}

	public function seed(Connection $db)
	{
	}

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function($modal) {
            $modal->data()->delete();
        });
    }

    public function data(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuData::class, 'menu_id', 'id');
    }
}
