<?php

declare(strict_types=1);

namespace App\Sms\Admin;

use App\Sms\Models\SmsTpl;
use App\Sms\Service\Enum\Type;
use Dux\Manage\Manage;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Validator\Data;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[\Dux\Resources\Attribute\Resource(app: 'admin', route: '/sms/tpl', name: 'sms.tpl')]
class Tpl extends Resources
{
    protected string $model = SmsTpl::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "label" => $item->label,
            "method" => Type::from($item->method)->name(),
            "type" => $item->type,
            "content" => $item->content,
            "tpl" => $item->tpl,
            "params" => $item->params,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('sms.tpl.validator.name', 'manage')],
        ];
    }


    #[Action(methods: 'GET', route: '/method')]
    public function method(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return send($response, 'ok', Type::list());
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "label" => $data->label,
            "name" => $data->name,
            "method" => $data->method,
            "type" => Type::from($data->method)->type(),
            "content" => $data->content ?: null,
            "tpl" => $data->tpl ?: null,
            "params" => $data->params ?: null,
        ];
    }
}
