<?php

namespace App\Content\Api;

use App\Tools\Service\Content;
use Dux\App;
use Dux\Auth\AuthService;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[RouteGroup(app: 'api', pattern: '/content/article')]
class Article
{

    #[Route(methods: 'GET', pattern: '')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();

        $list = \App\Content\Service\Article::page(where: [], classId: $params['class'], recId: $params['recommend'], image: $params['image'],keyword: $params['keyword'], tag: $params['tag'], limit: 20);


        $result = format_data($list, function ($item) {
            return [
                'id' => $item->id,
                'class_name' => $item->class->name,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'keywords' => $item->keywords,
                'descriptions' => $item->descriptions,
                'images' => $item->images,
                'source' => $item->sources?->name ?: $item->source,
                'source_avatar' => $item->sources?->avatar,
                'view' => $item->view + $item->virtual_view,
                'time' => $item->created_at->format('Y-m-d H:i:s'),
                'collect' => $item->collect,
                'comment' => $item->comment,
                'praise' => $item->praise,
                'extend' => $item->extend,
                'top' => $item->top
            ];
        });

        return send($response, 'ok', ...$result);
    }


    #[Route(methods: 'GET', pattern: '/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $info = \App\Content\Models\Article::query()->where('id', $args['id'])->first();
        if (!$info) {
            throw new ExceptionNotFound();
        }
        $info->view++;
        $info->save();

        $meta = App::apiEvent(self::class)->get('info.meta', $info, $request, $response, $args);
        
        return send($response, 'ok', [
            'id' => $info->id,
            'class_id' => $info->class_id,
            'source' => $info->sources?->name ?: $info->source,
            'source_avatar' => $info->sources?->avatar,
            'title' => $info->title,
            'keywords' => $info->keywords,
            'descriptions' => $info->descriptions,
            'content' => Content::fromHtml($info->content),
            'images' => $info->images,
            'view' => $info->view + $info->virtual_view,
            'time' => $info->created_at->format('Y-m-d H:i:s'),
            'collect' => $info->collect,
            'comment' => $info->comment,
            'praise' => $info->praise,
            'extend' => $info->extend
        ], $meta);
    }


    #[Route(methods: 'GET', pattern: '/{id}/poster')]
    public function qrcode(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];
        $userId = (new AuthService('member'))->id($request);
        $url = \App\Content\Service\Article::genQrcode($id, (int)$userId, $request);
        return send($response, 'ok', [
            'image' => $url,
        ]);
    }

}