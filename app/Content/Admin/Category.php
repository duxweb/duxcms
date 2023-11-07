<?php

declare(strict_types=1);

namespace App\Content\Admin;

use App\Content\Models\ArticleClass;
use App\Tools\Models\ToolsMagic;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/category', name: 'content.category')]
class Category extends Resources
{
	protected string $model = \App\Content\Models\ArticleClass::class;

    protected bool $tree = true;
    protected array $pagination = [
        'status' => false,
    ];

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "parent_id" => $item->parent_id,
            "name" => $item->name,
            "magic_id" => $item->magic_id,
            "children" => $item->children ? $item->children->map(function ($vo) {
                return $this->transform($vo);
            }) : []
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('content.category.validator.name', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "parent_id" => $data->parent_id ?: 0,
            "magic_id" => $data->magic_id,
		];
	}

    #[Action(methods: 'GET', route: '/{id}/magic')]
    public function magic(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $info = ArticleClass::query()->find($id);
        $magicInfo = ToolsMagic::query()->find($info->magic_id);
        if (!$magicInfo) {
            return send($response, 'ok');
        }
        $magicInfo->fields = \App\Tools\Service\Magic::formatConfig($magicInfo->fields);
        return send($response, 'ok', $magicInfo->toArray());
    }

    public function delBefore(mixed $info): void
    {
        $ids = ArticleClass::descendantsAndSelf($info->id)->pluck('id');
        if (\App\Content\Models\Article::query()->whereIn('class_id', $ids)->exists()) {
            throw new ExceptionBusiness(__('content.category.validator.article', 'manage'));
        }
    }
}
