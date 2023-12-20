<?php

namespace App\Member\Api;

use App\Member\Models\MemberUnion;
use Dux\App;
use Dux\Route\Attribute\Route;
use Dux\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Union
{

    #[Route(methods: 'POST', pattern: '/member/oauth/token', app: 'api')]
    public function token(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody(), [
            "type" => ["required", "三方类型不存在"],
            "code" => ["required", "三方登录code"],
        ]);
        $token = \App\Member\Service\Union::get($data->type, $data->code, $data->params ?: []);
        return send($response, 'ok', [
            'token' => $token
        ]);
    }

    #[Route(methods: 'POST', pattern: '/member/oauth/login', app: 'api')]
    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody(), [
            "token" => ["required", "请传递token"],
        ]);
        return send($response, "ok", \App\Member\Service\Union::Login((string)$data->token));
    }

    #[Route(methods: 'POST', pattern: '/member/oauth/bind', app: 'apiMember')]
    public function bind(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = Validator::parser($request->getParsedBody(), [
            "token" => ["required", "请传递token"],
        ]);
        $auth = $request->getAttribute("auth", []);
        \App\Member\Service\Union::bind($data->token, (int)$auth["id"]);
        return send($response, '绑定成功');
    }

    #[Route(methods: 'GET', pattern: '/member/oauth', app: 'apiMember')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $params = $request->getQueryParams();

        $info = MemberUnion::query()->where('type', $params['type'])->where('user_id', $auth['id'])->orderByDesc('updated_at')->first();

        return send($response, 'ok', [
            'info' => [
                'union_id' => $info->union_id,
                'open_id' => $info->open_id,
            ]
        ]);

    }

}