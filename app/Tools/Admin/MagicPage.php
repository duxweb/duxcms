<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsMagic;
use App\Tools\Models\ToolsMagicData;
use Dux\Handlers\ExceptionBusiness;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/tools/dataPage', name: 'tools.dataPage', auth: false, actions: false)]
class MagicPage
{
    protected string $model = ToolsMagicData::class;

    private ?object $info = null;

    public function init(ServerRequestInterface $request, ResponseInterface $response, array $args): void
    {
        $params = $request->getQueryParams();
        $this->info = ToolsMagic::query()->where('name', $params['magic'])->first();
    }

    #[Action(methods: 'GET', route: '')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->init($request, $response, $args);
        $info = ToolsMagicData::query()->where('magic_id', $this->info->id)->first();

        return send($response, 'ok', $info?->data ?: []);
    }

    #[Action(methods: ['POST', 'PUT'], route: '')]
    public function save(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->init($request, $response, $args);

        $validator = Validator::rule($this->info->fields ?: []);
        $requestData = $request->getParsedBody();
        $data = Validator::parser($requestData, $validator);
        ToolsMagicData::query()->where('magic_id', $this->info->id)->updateOrCreate([
            'magic_id' => $this->info->id,
        ], [
            'data' => $data->toArray()
        ]);
        return send($response, 'ok');
    }


}
