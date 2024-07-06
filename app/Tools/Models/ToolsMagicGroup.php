<?php

declare(strict_types=1);

namespace App\Tools\Models;

use App\System\Models\SystemUser;
use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class ToolsMagicGroup extends \Dux\Database\Model
{
    public $table = 'magic_group';

    use NodeTrait;

    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->string("label")->comment('分类标签')->nullable();
        $table->string("name")->comment('分类名')->nullable();
        $table->string("icon")->comment('图标')->nullable();
        $table->string("res")->comment('资源名')->nullable();
        $table->integer("sort")->comment('顺序')->nullable();
        NestedSet::columns($table);
        $table->timestamps();
    }

    public function seed(Connection $db)
    {
    }

    public function magics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ToolsMagic::class, 'group_id', 'id');
    }
}
