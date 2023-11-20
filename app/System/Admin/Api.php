<?php

declare(strict_types=1);

namespace App\System\Admin;

use App\System\Models\SystemApi;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/system/api', name: 'system.api')]
class Api extends Resources
{
	protected string $model = SystemApi::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "secret_id" => $item->secret_id,
            "secret_key" => $item->secret_key,
            "status" => (bool)$item->status,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('system.api.validator.name', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        $data = [
            "name" => $data->name,
        ];
        if (!$data->id) {
            $data = [...$data, ...[
                "secret_id" => random_int(10000000, 99999999),
                "secret_key" => bin2hex(random_bytes(16)),
            ]];
        }
        return $data;
    }
}
