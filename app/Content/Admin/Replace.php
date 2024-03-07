<?php

declare(strict_types=1);

namespace App\Content\Admin;

use App\Content\Models\ArticleReplace;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/content/replace', name: 'content.replace')]
class Replace extends Resources
{
    protected string $model = ArticleReplace::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "from" => $item->from,
            "to" => $item->to,
        ];
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "from" => ["required", __('content.replace.validator.from', 'manage')],
            "to" => ["required", __('content.replace.validator.to', 'manage')],
        ];
    }

    public function format(Data $data, ServerRequestInterface $request, array $args): array
    {
        return [
            "from" => $data->from,
            "to" => $data->to,
        ];
    }
}
