<?php

namespace App\Member\Api;

use App\Activity\Models\ActivityData;
use App\Member\Event\ContentEvent;
use App\Member\Models\MemberCollect;
use App\Member\Models\MemberComment;
use Dux\App;
use Dux\Auth\AuthService;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Dux\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;

#[RouteGroup(app: 'apiMember', pattern: '/member/collect')]
class Collect
{


    #[Route(methods: 'GET', pattern: '/{type}/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $auth = $request->getAttribute('auth');
        $userId = (int)$auth['id'];
        $event = new ContentEvent();
        App::event()->dispatch($event, 'member.content');
        $type = $event->getMapType($args['type']);
        if (!$type) {
            throw new ExceptionNotFound();
        }

        $count = \App\Member\Service\Collect::count($userId, $type, (int)$args['id']);
        return send($response, "ok", [
            'status' => (boolean)$count
        ]);
    }

    #[Route(methods: 'POST', pattern: '/{type}/{id}')]
    public function run(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $auth = $request->getAttribute('auth');
        $userId = (int)$auth['id'];
        if (!$userId) {
            throw new HttpUnauthorizedException($request);
        }
        $event = new ContentEvent();
        App::event()->dispatch($event, 'member.content');
        $type = $event->getMapType($args['type']);
        if (!$type) {
            throw new ExceptionNotFound();
        }

        App::db()->getConnection()->beginTransaction();
        try {
            \App\Member\Service\Collect::run($userId, $type, (int)$args['id']);
            App::db()->getConnection()->commit();
        } catch (\Exception $e) {
            App::db()->getConnection()->rollBack();
            throw $e;
        }
        return send($response, "ok");
    }
}