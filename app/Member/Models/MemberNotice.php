<?php

declare(strict_types=1);

namespace App\Member\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class MemberNotice extends Model
{
    public $table = 'member_notice';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->bigInteger('user_id')->nullable()->index();
        $table->tinyInteger('type')->comment('类型 0用户 1全部')->index();
        $table->string('image')->comment('封面图')->nullable();
        $table->string('title')->comment('标题');
        $table->string('desc')->comment('描述');
        $table->string('url')->comment('消息链接')->nullable();
        $table->timestamps();
    }


    public function seed(Connection $db)
    {
    }

    public function read(): HasMany
    {
        return $this->hasMany(MemberNoticeRead::class, 'notice_id', 'id');
    }
}
