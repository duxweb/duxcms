<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/source', name: 'content.source')]
class Source extends Resources
{
	protected string $model = \App\Content\Models\ArticleSource::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "avatar" => $item->avatar,
            'created_at' => $item->created_at->format('Y-m-d H:i'),
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('content.source.validator.name', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "avatar" => $data->avatar,
		];
	}
}
