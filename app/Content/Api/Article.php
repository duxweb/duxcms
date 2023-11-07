<?php

namespace App\Content\Api;

use App\Content\Models\ArticleSource;
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
        $query = \App\Content\Models\Article::query();
        $query->with(['sources']);
        $query->where('status', 1);
        if ($params['class']) {
            $query->where('class_id', $params['class']);
        }
        $list = $query->paginate(20);

        $result = format_data($list, function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'images' => $item->images,
                'source' => $item->sources?->name ?: $item->source,
                'source_avatar' => $item->sources?->avatar,
                'view' => $item->view + $item->virtual_view,
                'time' => $item->created_at->format('Y-m-d H:i:s'),
                'extend' => $item->extend
            ];
        });

        return send($response, 'ok', $result['data'], $list['meta']);
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

        return send($response, 'ok', [
            'id' => $info->id,
            'source' => $info->sources?->name ?: $info->source,
            'source_avatar' => $info->sources?->avatar,
            'title' => $info->title,
            'content' => $info->content,
            'images' => $info->images,
            'time' => $info->created_at->format('Y-m-d H:i:s'),
            'extend' => $info->extend
        ]);
    }

}