<?php

namespace App\Member\Admin;

use App\Member\Models\MemberLevel;
use App\System\Service\Config;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Route\Attribute\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[Resource(app: 'admin',  route: '/member/setting', name: 'member.setting', actions: false)]
class Setting {

    #[Action(methods: 'GET', route: '')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $config = \App\System\Service\Config::getValue("user_*", []);
        $config['user_level'] = (int) $config['user_level'];
        $config['user_code'] = (int) $config['user_code'];
        $config['user_register'] = (int)$config['user_register'];
        return send($response, "ok",$config);
    }

    #[Action(methods: 'PUT', route: '')]
    public function save(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        foreach ($data as $key => $vo) {
            Config::setValue($key, $vo);
        }
        return send($response, __('message.store', [
            '%name%' => __('member.setting.name', 'manage')
        ], "common"));
    }
}