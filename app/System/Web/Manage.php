<?php

namespace App\System\Web;

use Dux\App;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'web', pattern: '/manage')]
class Manage
{
    #[Route(methods: 'GET', pattern: '')]
    public function location(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $response->withStatus(302)->withHeader('Location', '/manage/');
    }

    #[Route(methods: 'GET', pattern: '/')]
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = \Dux\App::view('web');
        $data = json_decode(file_get_contents(public_path('/web/.vite/manifest.json')) ?: '', true);
        $vite = App::config('use')->get('vite', []);
        $lang = App::config('use')->get('lang', 'en-US');

        $manage =  App::config('use')->get('manage');
        $manage['indexName'] = $manage['indexName'] ?: 'system';
        $manage['sideType'] = $manage['sideType'] ?: 'app';
        $assign = [
            "title" => App::config('use')->get('app.name'),
            "lang" => $lang,
            'vite' => [
                'dev' => (bool)$vite['dev'],
                'port' => $vite['port'] ?: 5173,
            ],
            'manifest' => [
                'js' => $data['src/index.tsx']['file'],
                'css' => $data['style.css']['file'],
            ],
            'manage' => $manage,
        ];

        $html = $view->renderToString(dirname(__DIR__) . "/Views/Web/manage.html", $assign);

        return sendText($response, $html);
    }

}