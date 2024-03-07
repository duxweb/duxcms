<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/tags', name: 'content.tags', actions: ['list', 'show', 'delete'])]
class Tags extends Resources
{
	protected string $model = \App\Content\Models\ArticleTags::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "count" => $item->count,
            "view" => $item->view,
            "created_at" => $item->created_at->format('Y-m-d H:i'),
        ];
    }
}
