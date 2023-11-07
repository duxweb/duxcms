<?php

declare(strict_types=1);

namespace App\Content\Admin;

use App\Content\Models\ArticleClass;
use App\Tools\Models\ToolsMagic;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;
use voku\helper\HtmlDomParser;

#[Resource(app: 'admin',  route: '/content/article', name: 'content.article')]
class Article extends Resources
{
	protected string $model = \App\Content\Models\Article::class;

    public array $excludesMany = ['content', 'extend'];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            'class_id' => $item->class_id,
            "title" => $item->title,
            "images" => $item->images,
            "content" => $item->content,
            'source' => $item->source,
            'subtitle' => $item->subtitle,
            'view' =>$item->view,
            'virtual_view' =>$item->virtual_view,
            'status' => (bool)$item->status,
            'created_at' => $item->created_at->format('Y-m-d H:i'),
            'extend' => $item->extend
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {

        $classInfo = ArticleClass::query()->find($data->class_id);
        $magicInfo = ToolsMagic::query()->find($classInfo->magic_id);

        return [
            "title" => ["required", __('content.article.validator.title', 'manage')],
            ...Validator::rule($magicInfo?->fields ?: [])
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $images = $data->images;
        if ($data->images_auto && !$images) {
            $dom = HtmlDomParser::str_get_html($data->content);
            $elements = $dom->findMulti('img');
            foreach ($elements as $element) {
                $images[] = $element->getAttribute('src');
            }
        }

        return [
            "class_id" => $data->class_id,
            "title" => $data->title,
            'subtitle' => $data->subtitle,
            "images" => $images,
            "content" => $data->content,
            'source' => $data->source,
            'status' => $data->status,
            'virtual_view' =>$data->virtual_view ?: 0,
            'extend' => $data->extend
        ];
    }

    public function createAfter(Data $data, mixed $info): void
    {
        \App\Content\Service\Source::autoSave($data->source);
    }

    public function editAfter(Data $data, mixed $info): void
    {
        \App\Content\Service\Source::autoSave($data->source);
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
