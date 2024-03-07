<?php

declare(strict_types=1);

namespace App\Content\Admin;

use App\Content\Models\ArticleAttr;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/attr', name: 'content.attr')]
class Attr extends Resources
{
    protected string $model = ArticleAttr::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('content.attr.validator.name', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => $data->name,
        ];
    }
}
