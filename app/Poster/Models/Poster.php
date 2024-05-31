<?php

declare(strict_types=1);

namespace App\Poster\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

#[AutoMigrate]
class Poster extends \Dux\Database\Model
{
    public $table = 'poster';


    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string("title")->comment('海报标题');
        $table->json('data')->comment('海报数据')->nullable();
        $table->timestamps();
    }

    protected $casts = [
      'data' => 'array'
    ];


    public function seed(Connection $db)
    {
    }
}
