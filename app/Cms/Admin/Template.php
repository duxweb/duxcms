<?php

declare(strict_types=1);

namespace App\Cms\Admin;

use App\System\Service\Config;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Route\Attribute\Route;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/cms/template', name: 'cms.template')]
class Template
{

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $configList = glob(base_path('template/*/config.json'));
        $list = [];
        foreach ($configList as $config) {
            $data = json_decode(file_get_contents($config), true);
            $list[] = [
                "id" => basename(dirname($config)),
                "dir" => basename(dirname($config)),
                "name" => $data['theme']['name'],
                "help" => $data['theme']['help'],
            ];
        }
        return send($response, 'ok', $list);
    }

    #[Action(methods: 'GET', route: '/{id}')]
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['id'];
        $file = base_path('template/'.$name.'/config.json');
        $data = json_decode(file_get_contents($file), true);
        $config = Config::getJsonValue('theme_' . $name, []);

        return send($response, 'ok', $config, $data);
    }

    #[Action(methods: 'PUT', route: '/{id}')]
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $name = $args['id'];
        Config::setValue('theme_' . $name, $data);

        return send($response, __("message.edit", [
            "%name%" => __("cms.template.name", 'manage'),
        ], "common"));
    }

    #[Action(methods: 'PATCH', route: '/{id}')]
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $name = $args['id'];
        Config::setValue('theme', $name);

        return send($response, __("message.store", [
            "%name%" => __("cms.template.name", 'manage'),
        ], "common"));
    }

}
