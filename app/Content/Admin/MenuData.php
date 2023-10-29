<?php

declare(strict_types=1);

namespace App\Content\Admin;

use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/menuData', name: 'content.menuData')]
class MenuData extends Resources
{
	protected string $model = \App\Content\Models\MenuData::class;

    protected bool $tree = true;
    protected array $pagination = [
        'status' => false,
    ];

    public int $menuId = 0;

    public function init(ServerRequestInterface $request, ResponseInterface $response, array $args): void
    {
        $params = $request->getQueryParams();
        $this->menuId = (int)$params['menu_id'];
    }

    public function query(Builder $query): void
    {
        $query->where('menu_id', $this->menuId);
    }

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "menu_id" => $item->menu_id,
            "parent_id" => $item->parent_id,
            "title" => $item->title,
            "subtitle" => $item->subtitle,
            "image" => $item->image,
            "url" => $item->url,
            "children" => $item->children ? $item->children->map(function ($vo) {
                return $this->transform($vo);
            }) : []
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
            "title" => ["required", __('content.menuData.validator.title', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
            "menu_id" => $this->menuId,
		    "parent_id" => $data->parent_id,
            "title" => $data->title,
            "subtitle" => $data->subtitle,
            "image" => $data->image,
            "url" => $data->url,
		];
	}
}
