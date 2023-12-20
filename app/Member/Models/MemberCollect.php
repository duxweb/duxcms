<?php

declare(strict_types=1);

namespace App\Member\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class MemberCollect extends Model
{
    public $table = 'member_collect';

    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->bigInteger('user_id')->comment('用户id')->index();
        $table->string('has_type')->comment('关联类型')->index();
        $table->bigInteger('has_id')->comment('关联id')->index();
        $table->timestamps();
    }

    public function hastable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'has_type', 'has_id');
    }
}