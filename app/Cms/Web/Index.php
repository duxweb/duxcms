<?php

namespace App\Cms\Web;

use App\Cms\Service\Translator;
use App\System\Service\Config;
use Dux\App;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Latte\Essential\TranslatorExtension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RouteGroup(app: 'web', pattern: '/')]
class Index
{

    #[Route(methods: 'GET', pattern: '')]
    public function location(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = \Dux\App::view('web');

        $theme = Config::getValue('theme', 'default');
        $config = Config::getJsonValue('theme_' . $theme, []);
        self::langs($theme);
        $path = $request->getUri()->getPath();
        $this->theme();

        $html = $view->renderToString(base_path('theme/'.$theme.'/index.latte'), [
            'theme' => $config,
            'path' => $path
        ]);


        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: 'page/{name}[/{id}]')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $id = $args['id'];
        $name = $args['name'];
        $theme = Config::getValue('theme', 'default');
        $filePath = base_path('theme/' . $theme . '/' . $name . '.latte');
        if (!file_exists($filePath)) {
            throw new ExceptionNotFound();
        }
        self::langs($theme);
        $view = \Dux\App::view('web');
        $config = Config::getJsonValue('theme_' . $theme, []);
        $path = $request->getUri()->getPath();
        $this->theme();

        $html = $view->renderToString($filePath, [
            'theme' => $config,
            'query' => $params,
            'name' => $name,
            'id' => $id,
            'path' => $path
        ]);

        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: 'map/theme/{name}/topic')]
    public function topic(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['name'];
        $file = base_path('theme/' . $name . '/topic.jpg');

        if (!is_file($file)) {
            throw new ExceptionNotFound();
        }
        $imageContent = file_get_contents(base_path('theme/' . $name . '/topic.jpg'));

        $response = $response->withHeader('Content-Type', 'image/jpeg')
            ->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')));
        $response->getBody()->write($imageContent);
        return $response;
    }

    private function langs(string $theme): void
    {
        $dirPath = base_path('theme/' . $theme . '/langs');
        $files = glob($dirPath . "/*.yaml");
        if (!$files) {
            return;
        }
        foreach ($files as $file) {
            $name = basename($file, '.yaml');
            App::trans()->addResource('yaml', $file, $name, 'theme');
        }

        $translator = new Translator();
        $extension = new TranslatorExtension(
            $translator->translate(...),
        );
        App::view('web')->addExtension($extension);
    }

    private function theme(): void
    {
        $theme = Config::getValue('theme', 'default');
        $themePath = base_path("theme/$theme");
        $linkPath = base_path("public/theme");

        if (is_link($linkPath)) {
            $link = readlink($linkPath);
            if ($link) {
                $baseName = pathinfo($link);
                if ($baseName != $theme) {
                    unlink($linkPath);
                }
            }
        }

        if (!is_link($linkPath)) {
            symlink($themePath, $linkPath);
        }

    }

}