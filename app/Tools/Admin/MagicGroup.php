<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsMagicGroup;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/magicGroup',  name: 'tools.magicGroup')]
class MagicGroup extends Resources
{
	protected string $model = ToolsMagicGroup::class;


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
            "label" => $item->label,
            "icon" => $item->icon,
            "res" => $item->res,
            "sort" => $item->sort,
            "children" => $item->children ? $item->children->map(function ($vo) {
                return $this->transform($vo);
            }) : []
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('tools.magicGroup.validator.name', 'manage')],
            "label" => ["required",__('tools.magicGroup.validator.label', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "label" => $data->label,
            "icon" => $data->icon,
            "parent_id" => $data->parent_id,
            "res" => $data->res,
            "sort" => $data->sort,
		];
	}

    public function createAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function editAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function storeAfter(Data $data, mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }

    public function delAfter(mixed $info): void
    {
        \App\Tools\Service\Magic::clean();
    }
}
