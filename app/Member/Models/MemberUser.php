<?php

declare(strict_types=1);

namespace App\Member\Models;

use App\Member\Event\UserEvent;
use Carbon\Carbon;
use Dux\App;
use Dux\Database\Attribute\AutoMigrate;

#[AutoMigrate]
class MemberUser extends \Dux\Database\Model
{
    public $table = 'member_user';

    protected $casts = ['login_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($info) {
            // 清理关联数据
            MemberUnion::query()->where('user_id', $info->id)->delete();
            MemberNotice::query()->where('user_id', $info->id)->delete();
            MemberNoticeRead::query()->where('user_id', $info->id)->delete();
            // NOTE member.delete （用户删除事件）
            App::event()->dispatch(new UserEvent($info), 'member.delete');
        });
    }

    public function migration(\Illuminate\Database\Schema\Blueprint $table)
    {
        $table->id();
        $table->bigInteger('level_id')->nullable()->comment('等级')->index();
        $table->string('nickname')->nullable()->comment('昵称');
        $table->string('email')->nullable()->comment('邮箱');
        $table->string('tel')->nullable()->comment('手机号');
        $table->string('password')->nullable()->comment('密码');
        $table->string('avatar')->nullable()->comment('头像');
        $table->tinyInteger('sex')->default(0)->comment('性别');
        $table->date('birthday')->nullable()->comment('生日');
        $table->integer('growth')->nullable()->comment('成长值');
        $table->timestamp('login_at')->nullable()->comment('登录日期');
        $table->string('login_ip')->nullable()->comment('登录ip');
        $table->boolean('status')->default(true)->comment('状态');
        $table->string('extend_1')->nullable()->comment('预留扩展');
        $table->string('extend_2')->nullable()->comment('预留扩展');
        $table->string('extend_3')->nullable()->comment('预留扩展');
        $table->string('extend_4')->nullable()->comment('预留扩展');
        $table->string('extend_5')->nullable()->comment('预留扩展');
        $table->timestamps();
    }

    public function level(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MemberLevel::class, 'id', 'level_id');
    }

    public function getSexNameAttribute(): string
    {
        return match ($this->sex) {
            default => '保密',
            1 => '男',
            2 => '女'
        };
    }

    public function getAgeAttribute(): string
    {
        if (!$this->birthday) {
            return '未知';
        }
        $date = Carbon::parse($this->birthday);
        return (string)$date->diffInYears();
    }


}
