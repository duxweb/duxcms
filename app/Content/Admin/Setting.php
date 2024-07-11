<?php

namespace App\Content\Admin;

use App\System\Service\Config;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/content/setting', name: 'mall.setting', actions: false)]
class Setting
{

    #[Action(methods: 'GET', route: '')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $config = \App\System\Service\Config::getJsonValue("content", []);

        return send($response, "ok", $config);
    }

    #[Action(methods: 'PUT', route: '')]
    public function save(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        Config::setValue("content", $data);
        return send($response, "保存配置成功");
    }
}