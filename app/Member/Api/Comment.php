<?php

namespace App\Member\Api;

use App\Member\Event\ContentEvent;
use App\Member\Models\MemberComment;
use App\Member\Models\MemberPraise;
use Dux\App;
use Dux\Auth\AuthService;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Dux\Validator\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;

#[RouteGroup(app: 'api', pattern: '/member/comment')]
class Comment
{
    #[Route(methods: 'GET', pattern: '/{type}/{id}')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $event = new ContentEvent();
        App::event()->dispatch($event, 'member.content');
        $type = $event->getMapType($args['type']);
        if (!$type) {
            throw new ExceptionNotFound();
        }
        $list = MemberComment::query()->with(['user', 'parent'])->where('has_type', $type)->where('has_id', $args['id'])->where('status', 1)->get();


        $userId = (new AuthService('member'))->id($request);
        $hasIds = [];
        if ($userId) {
            $commentIds = $list->pluck('id');
            $hasIds = MemberPraise::query()->where('has_type', MemberComment::class)->whereIn('has_id', $commentIds)->where('user_id', $userId)->pluck('has_id')->toArray();
        }

        $data = format_data($list, function ($item) use ($hasIds) {
            return [
                "id" => $item->id,
                'parent_id' => $item->parent_id,
                'nickname' => $item->user->nickname,
                'avatar' => $item->user->avatar,
                'content' => $item->content,
                'praise' => $item->praise,
                'is_praise' => in_array($item->id, $hasIds),
                'parent' => $item->parent  ? [
                    'nickname' => $item->parent->user->nickname,
                    'avatar' => $item->parent->user->avatar,
                ] : null,
                'time' => $item->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        $data = \App\Member\Service\Comment::buildFlatTree($data['data']);

        return send($response, "ok", $data);
    }


    #[Route(methods: 'POST', pattern: '/{type}/{id}')]
    public function push(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?: [];
        $userId = (new AuthService('member'))->id($request);
        if (!$userId) {
            throw new HttpUnauthorizedException($request);
        }
        $event = new ContentEvent();
        App::event()->dispatch($event, 'member.content');
        $type = $event->getMapType($args['type']);
        if (!$type) {
            throw new ExceptionNotFound();
        }
        $data = Validator::parser($data, [
            "content" => ["required", "请输入评论内容"],
        ]);

        App::db()->getConnection()->beginTransaction();
        try {
            \App\Member\Service\Comment::push($userId, $type, (int)$args['id'], $data->content ?: '', (int)$data->reply_id);
            App::db()->getConnection()->commit();
        } catch (\Exception $e) {
            App::db()->getConnection()->rollBack();
            throw $e;
        }
        return send($response, "ok");
    }
}