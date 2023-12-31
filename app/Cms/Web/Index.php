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

        $html = $view->renderToString(base_path('theme/default/index.latte'), [
            'theme' => $config,
        ]);
        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: 'theme/{path:.*}')]
    public function template(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (str_contains($args['path'], '..')) {
            throw new ExceptionNotFound();
        }
        $theme = Config::getValue('theme', 'default');
        $filePath = base_path('theme/' . $theme . '/' . $args['path']);
        if (!file_exists($filePath)) {
            throw new ExceptionNotFound();
        }
        $fileContent = file_get_contents($filePath);
        $response->getBody()->write($fileContent);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return match ($extension) {
            'css' => $response->withHeader('Content-Type', 'text/css'),
            'js' => $response->withHeader('Content-Type', 'application/javascript'),
            default => $response->withHeader('Content-Type', mime_content_type($filePath)),
        };
    }

    #[Route(methods: 'GET', pattern: 'page/{name}')]
    public function page(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $name = $args['name'];
        $theme = Config::getValue('theme', 'default');
        $filePath = base_path('theme/' . $theme . '/' . $name . '.latte');
        if (!file_exists($filePath)) {
            throw new ExceptionNotFound();
        }

        $view = \Dux\App::view('web');
        $config = Config::getJsonValue('theme_' . $theme, []);
        self::langs($theme);
        $html = $view->renderToString($filePath, [
            'theme' => $config,
            'query' => $params,
        ]);
        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: 'page/{name}/{id}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $id = $args['id'];
        $name = $args['name'];
        $theme = Config::getValue('theme', 'default');
        $filePath = base_path('theme/' . $theme . '/' . $name . '-info.latte');
        if (!file_exists($filePath)) {
            throw new ExceptionNotFound();
        }
        $view = \Dux\App::view('web');
        $config = Config::getJsonValue('theme_' . $theme, []);

        self::langs($theme);
        $html = $view->renderToString($filePath, [
            'theme' => $config,
            'query' => $params,
            'name' => $name,
            'id' => $id
        ]);
        return sendText($response, $html);
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

}