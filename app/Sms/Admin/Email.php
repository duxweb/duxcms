<?php

declare(strict_types=1);

namespace App\Sms\Admin;

use App\Sms\Models\SmsEmail;
use App\Sms\Service\Enum\Type;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Validator\Data;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[\Dux\Resources\Attribute\Resource(app: 'admin', route: '/sms/email', name: 'sms.email')]
class Email extends Resources
{
    protected string $model = SmsEmail::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
            "label" => $item->label,
            "content" => $item->content,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "name" => ["required", __('sms.tpl.validator.name', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "label" => $data->label,
            "name" => $data->name,
            "content" => $data->content ?: null,
        ];
    }
}
