<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/menu', name: 'content.menu')]
class Menu extends Resources
{
	protected string $model = \App\Content\Models\Menu::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "title" => $item->title,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('content.menu.validator.name', 'manage')],
            "title" => ["required", __('content.menu.validator.title', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "title" => $data->title,
		];
	}
}
