<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsMagicSource;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/magicSource',  name: 'tools.magicSource')]
class MagicSource extends Resources
{
	protected string $model = ToolsMagicSource::class;



    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "type" => $item->type,
            "data" => $item->type == 'data' ? json_encode($item->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $item->data,
            "url" => $item->url,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
            "name" => ["required", __('tools.magicSource.validator.name', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => $data->name,
            "type" => $data->type,
            "data" => $data->type == 'data' ? json_decode($data->data, true) : $data->data,
		];
	}


    #[Action(methods: 'GET', route: '/system')]
    public function source(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $list = array_map(function ($item) {
            return [
                'label' => $item['label'],
                'value' => $item['name']
            ];
        }, \App\Tools\Service\Magic::source());
        return send($response, 'ok', array_values($list));
    }
}
