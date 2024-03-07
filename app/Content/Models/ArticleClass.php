<?php

declare(strict_types=1);

namespace App\Content\Models;

use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

#[AutoMigrate]
class ArticleClass extends \Dux\Database\Model
{
	public $table = 'article_class';

    use NodeTrait;

	public function migration(Blueprint $table)
	{
		$table->id();
        $table->bigInteger('magic_id')->nullable();
        $table->string('name');
        $table->string('image')->nullable();
        $table->json('tops')->nullable();
        NestedSet::columns($table);
		$table->timestamps();
	}

	public function seed(Connection $db)
	{
	}

    protected $casts = [
      'tops' => 'array'
    ];

}
