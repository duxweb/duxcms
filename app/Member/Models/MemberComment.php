<?php

declare(strict_types=1);

namespace App\Member\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class MemberComment extends \Dux\Database\Model
{
    public $table = 'member_comment';


    public function migration(\Illuminate\Database\Schema\Blueprint $table): void
    {
        $table->id();
        $table->bigInteger('parent_id')->comment('上级id')->nullable();
        $table->bigInteger('user_id')->comment('用户id');
        $table->string('has_type')->comment('关联类型')->index();
        $table->bigInteger('has_id')->comment('关联id')->index();
        $table->string('content')->comment('评论内容');
        $table->boolean('status')->comment('状态')->default(0);
        $table->bigInteger('praise')->comment('点赞')->default(0);
        $table->timestamps();
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($model) {
            MemberComment::query()
                ->where('has_type', MemberComment::class)
                ->where('has_id', $model->id)
                ->delete();
        });
    }

    public function hastable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'has_type', 'has_id');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }


    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MemberUser::class, 'id', 'user_id');
    }
}