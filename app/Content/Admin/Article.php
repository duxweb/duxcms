<?php

declare(strict_types=1);

namespace App\Content\Admin;

use App\Content\Models\ArticleClass;
use App\System\Service\Config;
use App\Tools\Models\ToolsMagic;
use Dux\Handlers\ExceptionBusiness;
use Dux\Package\Package;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Utils\Content;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/content/article', name: 'content.article')]
class Article extends Resources
{
    protected string $model = \App\Content\Models\Article::class;

    public array $excludesMany = ['content', 'extend'];

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $query->orderByDesc('id');
        $query->with([
            'class',
            'class.ancestors'
        ]);
        $params = $request->getQueryParams();
        if ($params['keyword']) {
            $query->where('title', 'like', '%' . $params['keyword'] . '%');
        }
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


    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            'class_id' => $item->class_id,
            'class_name' => array_filter([...($item->class?->ancestors?->pluck('name')->toArray() ?: []), $item->class->name]),
            "title" => mb_substr($item->title, 0, 500),
            "images" => $item->images,
            "content" => $item->content,
            'source' => $item->source,
            'subtitle' => $item->subtitle,
            'view' => $item->view,
            'virtual_view' => $item->virtual_view,
            'status' => (bool)$item->status,
            'created_at' => $item->created_at->format('Y-m-d H:i'),
            'keywords' => $item->keywords ? explode(',', $item->keywords) : [],
            'descriptions' => $item->descriptions,
            'attrs' => $item->attrs?->pluck('id')->toArray(),
            'extend' => $item->extend
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {

        $classInfo = ArticleClass::query()->find($data->class_id);
        $magicInfo = ToolsMagic::query()->find($classInfo->magic_id);
        Validator::parser($data->extend ?: [], Validator::rule($magicInfo?->fields ?: []));

        return [
            "class_id" => ["required", __('content.article.validator.class_id', 'manage')],
            "title" => ["required", __('content.article.validator.title', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $content = html_entity_decode($data->content);

        if ($data->images_auto && !$data->images) {
            $images = Content::extractImages($content);
            $data->images = array_slice($images, 0, 3);
        }

        if (!$data->descriptions) {
            $data->descriptions = Content::extractDescriptions($content);
        }

        return [
            "class_id" => $data->class_id,
            "title" => $data->title,
            'subtitle' => $data->subtitle,
            "images" => $data->images,
            "content" => $content,
            'source' => $data->source,
            'status' => $data->status,
            'virtual_view' => $data->virtual_view ?: 0,
            'keywords' => $data->keywords ? implode(',', $data->keywords) : '',
            'descriptions' => $data->descriptions,
            'extend' => $data->extend,
        ];
    }



    public function createAfter(Data $data, mixed $info): void
    {

        \App\Content\Service\Source::autoSave($data->source);
        $info->retag($data->keywords);
        $info->attrs()->sync($data->attrs ?: []);
    }

    public function editAfter(Data $data, mixed $info): void
    {
        \App\Content\Service\Source::autoSave($data->source);
        $info->retag($data->keywords);
        $info->attrs()->sync($data->attrs ?: []);
    }

    public function delBefore(mixed $info): void
    {
        $info->untag();
    }

}
