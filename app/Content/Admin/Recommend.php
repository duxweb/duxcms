<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/content/recommend', name: 'content.recommend')]
class Recommend extends Resources
{
    protected string $model = \App\Content\Models\ArticleRecommend::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "created_at" => $item->created_at->format('Y-m-d H:i'),
            'articles' => $item->articles?->pluck('id')->toArray() ?: []
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('content.recommend.validator.name', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => $data->name,
        ];
    }


    public function createAfter(Data $data, mixed $info): void
    {
        $info->articles()->sync([]);
        if ($data->articles) {
            foreach ($data->articles as $sort => $id) {
                $info->articles()->attach($id, ['sort' => $sort ?: 0]);
            }
        }
    }

    public function editAfter(Data $data, mixed $info): void
    {
        $info->articles()->sync([]);
        if ($data->articles) {
            foreach ($data->articles as $sort => $id) {
                $info->articles()->attach($id, ['sort' => $sort ?: 0]);
            }
        }
    }
}
