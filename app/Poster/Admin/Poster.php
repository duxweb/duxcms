<?php

declare(strict_types=1);

namespace App\Poster\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/poster/design', name: 'poster.design')]
class Poster extends Resources
{
	protected string $model = \App\Poster\Models\Poster::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "title" => $item->title,
            "data" => $item->data,
        ];
    }


    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "title" => ["required", __('tools.poster.validator.title', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "title" => $data->title,
            "data" => $data->data
		];
	}
}
