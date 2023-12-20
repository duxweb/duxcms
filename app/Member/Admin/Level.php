<?php

declare(strict_types=1);

namespace App\Member\Admin;

use App\Member\Models\MemberLevel;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/member/level', name: 'member.level')]
class Level extends Resources
{
	protected string $model = MemberLevel::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "growth" => $item->growth ?: 0,
            "type" => $item->type
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => $data->name,
            "growth" => $data->growth ?: 0,
            "type" => $data->type ?: 0
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('member.level.validator.name', 'manage')],
        ];
    }
}
