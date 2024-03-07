<?php

namespace App\Content\Api;

use App\Member\Service\Foot;
use Dux\App;
use Dux\Auth\AuthService;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Pelago\Emogrifier\CssInliner;
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
        $query->orderByDesc('id');
        $query->with(['sources']);
        $query->where('status', 1);
        if ($params['class']) {
            $query->where('class_id', $params['class']);
        }
        if ($params['recommend']) {
            $query->whereHas('recommend', function ($query) use ($params) {
                $query->where('id', $params['recommend']);
            });
        }
        if ($params['keyword']) {
            $query->where(function ($query) use ($params) {
                $query->where('title', 'like', '%'.$params['keyword'].'%')->orWhere('descriptions', 'like', '%'.$params['keyword'].'%')->orWhere('keywords', 'like', '%'.$params['keyword'].'%');
            });
        }
        $list = $query->paginate(20);

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
                'extend' => $item->extend
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

        $content = $info->content;
        $css = file_get_contents(__DIR__ . '/../Other/style.css');

        $visualHtml = CssInliner::fromHtml('<div class="typo">'.$content.'</div>')->inlineCss($css)->renderBodyContent();

        $meta = App::apiEvent(self::class)->get('info.meta', $info, $request, $response, $args);


        return send($response, 'ok', [
            'id' => $info->id,
            'source' => $info->sources?->name ?: $info->source,
            'source_avatar' => $info->sources?->avatar,
            'title' => $info->title,
            'keywords' => $info->keywords,
            'descriptions' => $info->descriptions,
            'content' => $visualHtml,
            'images' => $info->images,
            'view' => $info->view + $info->virtual_view,
            'time' => $info->created_at->format('Y-m-d H:i:s'),
            'collect' => $info->collect,
            'comment' => $info->comment,
            'praise' => $info->praise,
            'extend' => $info->extend
        ], $meta);
    }

}