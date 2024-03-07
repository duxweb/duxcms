<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Utils\Content;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/page', name: 'content.page')]
class Page extends Resources
{
	protected string $model = \App\Content\Models\Page::class;

    public array $excludesMany = ['content'];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            'name' => $item->name,
            "title" => $item->title,
            "image" => $item->image,
            "content" => $item->content,
            'subtitle' => $item->subtitle,
            'virtual_view' =>$item->virtual_view,
            'status' => (bool)$item->status,
            'created_at' => $item->created_at->format('Y-m-d H:i'),
            'keywords' => $item->keywords ? explode(',', $item->keywords) : [],
            'descriptions' => $item->descriptions,
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {

        $content = html_entity_decode($data->content);

        if (!$data->descriptions) {
            $data->descriptions = Content::extractDescriptions($content);
        }

        return [
            "name" => $data->name,
            "title" => $data->title,
            'subtitle' => $data->subtitle,
            "image" => $data->image,
            "content" => $data->content,
            'status' => $data->status,
            'virtual_view' =>$data->virtual_view ?: 0,
            'keywords' => $data->keywords ? implode(',', $data->keywords) : '',
            'descriptions' => $data->descriptions,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "title" => ["required", __('content.page.validator.title', 'manage')],
            "name" => ["required", __('content.page.validator.name', 'manage')],
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();
        if ($params['tab'] == 1) {
            $query->where('status', 1);
        }
        if ($params['tab'] == 2) {
            $query->where('status', 0);
        }
    }

}
