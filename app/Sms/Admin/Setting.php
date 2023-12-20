<?php

namespace App\Sms\Admin;

use App\System\Service\Config;
use Dux\Resources\Attribute\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[\Dux\Resources\Attribute\Resource(app: 'admin', route: '/sms/setting', name: 'sms.setting', actions: false)]
class Setting {

    #[Action(methods: 'GET', route: '')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $config = \App\System\Service\Config::getValue("sms_*", []);
        return send($response, "ok", $config);
    }

    #[Action(methods: 'PUT', route: '')]
    public function save(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        foreach ($data as $key => $vo) {
            Config::setValue($key, $vo);
        }
        return send($response, __('message.store', [
            '%name%' => __('sms.setting.name', 'manage')
        ], "common"));
    }
}