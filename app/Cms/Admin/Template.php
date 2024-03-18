<?php

declare(strict_types=1);

namespace App\Cms\Admin;

use App\System\Service\Config;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Mimey\MimeTypes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

#[Resource(app: 'admin', route: '/cms/theme', name: 'cms.theme', actions: false)]
class Template
{

    #[Action(methods: 'GET', route: '')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $configList = glob(base_path('theme/*/config.json'));
        $list = [];
        foreach ($configList as $config) {
            $data = json_decode(file_get_contents($config), true);
            $name = basename(dirname($config));
            $list[] = [
                "id" => $name,
                "dir" => $name,
                "name" => $data['theme']['name'],
                "help" => $data['theme']['help'],
                'image' =>  "/map/theme/{$name}/topic"
            ];
        }
        return send($response, 'ok', $list);
    }

    #[Action(methods: 'GET', route: '/{id}')]
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['id'];
        $file = base_path('theme/' . $name . '/config.json');
        $data = json_decode(file_get_contents($file), true);
        $config = Config::getJsonValue('theme_' . $name, []);

        return send($response, 'ok', $config, $data);
    }


    #[Action(methods: 'GET', route: '/{id}/config')]
    public function config(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['id'];
        $file = base_path('theme/' . $name . '/config.json');
        $data = json_decode(file_get_contents($file), true);

        return send($response, 'ok', $data);
    }

    #[Action(methods: 'PUT', route: '/{id}')]
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $name = $args['id'];
        Config::setValue('theme_' . $name, $data);

        return send($response, __("message.edit", [
            "%name%" => __("cms.theme.name", 'manage'),
        ], "common"));
    }

    #[Action(methods: 'PATCH', route: '/{id}')]
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['id'];
        Config::setValue('theme', $name);

        return send($response, __("message.store", [
            "%name%" => __("cms.theme.name", 'manage'),
        ], "common"));
    }


}
