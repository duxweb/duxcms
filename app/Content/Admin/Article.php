<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/article', name: 'content.article')]
class Article extends Resources
{
	protected string $model = \App\Content\Models\Article::class;

    public array $excludesMany = ['content'];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            'class_id' => $item->class_id,
            "title" => $item->title,
            "image" => $item->image,
            "content" => $item->content,
            'author' => $item->author,
            'subtitle' => $item->subtitle,
            'view' =>$item->view,
            'virtual_view' =>$item->virtual_view,
            'status' => (bool)$item->status,
            'created_at' => $item->created_at->format('Y-m-d H:i')
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "class_id" => $data->class_id,
            "title" => $data->title,
            'subtitle' => $data->subtitle,
            "image" => $data->image,
            "content" => $data->content,
            'author' => $data->author,
            'status' => $data->status,
            'virtual_view' =>$data->virtual_view ?: 0,
        ];
    }


    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "title" => ["required", __('content.article.validator.title', 'admin')],
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();
        if ($params['class_id']) {
            $query->where('class_id', $params['class_id']);
        }
        if ($params['tab'] == 1) {
            $query->where('status', 1);
        }
        if ($params['tab'] == 2) {
            $query->where('status', 0);
        }
    }

}
