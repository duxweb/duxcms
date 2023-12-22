<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class MenuData extends \Dux\Database\Model
{
	public $table = 'menu_data';

    use NodeTrait;

    protected function getScopeAttributes(): array
    {
        return ['menu_id'];
    }

	public function migration(Blueprint $table)
	{
		$table->id();
        $table->bigInteger('menu_id')->comment('菜单ID');
        $table->string('title')->comment('菜单标题');
        $table->string('subtitle')->nullable()->comment('菜单子标题');
        $table->string('image')->nullable()->comment('菜单描述');
        $table->string('url')->nullable()->comment('菜单链接');
        NestedSet::columns($table);
		$table->timestamps();
	}


	public function seed(Connection $db)
	{
	}

    public function menu(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}
