<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/category', name: 'content.category')]
class Category extends Resources
{
	protected string $model = \App\Content\Models\ArticleClass::class;

    protected bool $tree = true;
    protected array $pagination = [
        'status' => false,
    ];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "parent_id" => $item->parent_id,
            "name" => $item->name,
            "children" => $item->children ? $item->children->map(function ($vo) {
                return $this->transform($vo);
            }) : []
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", "请输入名称"],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "parent_id" => $data->parent_id ?: 0,
		];
	}
}
