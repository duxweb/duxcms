<?php

namespace App\Cms\Web;

use App\Cms\Service\Translator;
use App\System\Service\Config;
use Carbon\Carbon;
use Dux\App;
use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Latte\Essential\TranslatorExtension;
use Latte\Loaders\FileLoader;
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
        $this->theme($view);

        $cookieParams = $request->getCookieParams();
        $html = $view->renderToString('index.latte', [
            'theme' => $config,
            'path' => $path,
            'cookie' => $cookieParams,
            'headers' => $request->getHeaders(),
        ]);


        return sendText($response, $html);
    }

    #[Route(methods: 'GET', pattern: 'page/{params:.*}')]
    public function info(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $theme = Config::getValue('theme', 'default');


        $path = $args['params'];
        $path = str_replace('.', '', $path);
        $filePath = base_path('theme/' . $theme . '/' . $path . '.latte');
        if (!file_exists($filePath)) {
            // 文件不存在则获取 id
            $paths = explode('/', $path);
            $id = end($paths);
            $paths = array_slice($paths, 0, -1);
            $path = implode('/', $paths);
            $filePath = base_path('theme/' . $theme . '/' . $path . '.latte');
            if (!file_exists($filePath)) {
                throw new ExceptionNotFound();
            }
            $name = $path;
        }else {
            $id = null;
            $name = $path;
        }

        $paths = explode('/', $path);
        if ($paths[0] == 'components' || $paths[0] == 'langs') {
            throw new ExceptionNotFound();
        }

        self::langs($theme);
        $view = \Dux\App::view('web');
        $config = Config::getJsonValue('theme_' . $theme, []);

        $path = $request->getUri()->getPath();
        $this->theme($view);

        $mergeUrl = function ($str, $query = []) use ($params) {
            $urls = $params ?: [];
            $urls = array_filter([...$urls, ...$query]);
            return $str . ($urls ? '?' . http_build_query($urls) : '');
        };

        $cookieParams = $request->getCookieParams();

        $html = $view->renderToString($name . '.latte', [
            'theme' => $config,
            'query' => $params,
            'name' => $name,
            'id' => $id,
            'path' => $path,
            'mergeUrl' => $mergeUrl,
            'cookie' => $cookieParams,
            'headers' => $request->getHeaders(),
            'request' => $request
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

    private function theme($view): void
    {
        $theme = Config::getValue('theme', 'default');

        $loader = new FileLoader(base_path('theme/' . $theme));
        $view->setLoader($loader);

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