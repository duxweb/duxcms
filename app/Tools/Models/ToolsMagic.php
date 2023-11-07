<?php

declare(strict_types=1);

namespace App\Tools\Models;

use App\System\Models\SystemRole;
use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsMagic extends \Dux\Database\Model
{
    public $table = 'magic';

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->bigInteger("group_id")->comment('分组id')->index()->nullable();
        $table->string("label")->comment('标签')->index();
        $table->string("name")->comment('数据名')->index();
        $table->string("type")->comment('数据类型')->default('common');
        $table->string("tree_label")->comment('数据类型')->nullable();
        $table->boolean("inline")->comment('附属模型')->nullable();
        $table->boolean("page")->comment('页面操作')->default(false);
        $table->json("external")->comment('数据权限')->nullable();
        $table->json('fields')->comment('数据字段')->nullable();
        $table->timestamps();
    }

    public function seed(Connection $db)
    {
    }

    protected $casts = [
        'fields' => 'array',
        'external' => 'array'
    ];

    public function group(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ToolsMagicGroup::class, 'id', 'group_id');
    }
}
