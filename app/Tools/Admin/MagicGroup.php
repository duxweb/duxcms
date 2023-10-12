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

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "label" => $item->label,
            "icon" => $item->icon,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('tools.magicGroup.validator.name', 'manage')],
            "label" => ["required",__('tools.magicGroup.validator.label', 'manage')],
            "icon" => ["required", __('tools.magicGroup.validator.icon', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "label" => $data->label,
            "icon" => $data->icon,
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
