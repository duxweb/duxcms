<?php

namespace App\Member\Api;

use App\Member\Models\MemberNotice;
use App\Member\Models\MemberNoticeRead;
use Dux\App;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'apiMember', pattern: '/member/notice')]
class Notice
{
    #[Route(methods: 'POST', pattern: '/read')]
    public function read(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getParsedBody();
        $ids = array_filter((array)$params['ids']);
        $auth = $request->getAttribute('auth');
        \App\Member\Service\Notice::read((int)$auth['id'], $ids);
        return send($response, "ok");
    }

    #[Route(methods: 'GET', pattern: '')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $result = \App\Member\Service\Notice::list((int)$auth['id']);
        return send($response, "ok", $result['data'], $result['meta']);
    }

    #[Route(methods: 'GET', pattern: '/num')]
    public function num(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $auth = $request->getAttribute('auth');
        $ids = MemberNotice::query()->where('user_id', (int)$auth['id'])->orWhere('type', 1)->pluck('id')->toArray();
        $noticeIds = MemberNoticeRead::query()->whereIn('notice_id', $ids)->where('user_id', (int)$auth['id'])->pluck('notice_id')->toArray();
        $result = array_diff($ids, $noticeIds);

        return send($response, "ok", [
            'num' => count($result)
        ]);
    }
}