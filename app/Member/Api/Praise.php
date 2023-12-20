<?php

namespace App\Member\Api;

use App\Member\Event\ContentEvent;
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

#[RouteGroup(app: 'api', pattern: '/member/praise')]
class Praise
{
    #[Route(methods: 'POST', pattern: '/{type}/{id}')]
    public function run(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $userId = (new AuthService('member'))->id($request);
        if (!$userId) {
            throw new HttpUnauthorizedException($request);
        }
        $event = new ContentEvent();
        App::event()->dispatch($event, 'member.content');
        $type = $event->getMapType($args['type']);


        App::db()->getConnection()->beginTransaction();
        try {
            $status = \App\Member\Service\Praise::run($userId, $type, (int)$args['id']);
            App::db()->getConnection()->commit();
        } catch (\Exception $e) {
            App::db()->getConnection()->rollBack();
            throw $e;
        }

        return send($response, "ok", [
            'status' => $status
        ]);
    }
}