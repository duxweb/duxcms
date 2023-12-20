<?php

declare(strict_types=1);

namespace App\Member\Models;

use Dux\Database\Attribute\AutoMigrate;
use Dux\Database\Model;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class MemberNoticeRead extends Model
{
    public $table = 'member_notice_read';


    public function migration(Blueprint $table)
    {
        $table->bigInteger('notice_id')->index();
        $table->bigInteger('user_id')->index();
    }


    public function seed(Connection $db)
    {
    }
}
