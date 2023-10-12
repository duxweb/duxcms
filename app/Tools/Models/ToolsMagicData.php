<?php

declare(strict_types=1);

namespace App\Tools\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class ToolsMagicData extends \Dux\Database\Model
{
    public $table = 'magic_data';

    public function migration(Blueprint $table): void
    {
        $table->id();
        $table->bigInteger("magic_id")->comment('关联id')->index();
        $table->json("data")->comment('数据')->nullable();
        $table->timestamps();
    }

    public function seed(Connection $db)
    {
    }

    protected $casts = [
        'data' => 'array',
    ];
}
