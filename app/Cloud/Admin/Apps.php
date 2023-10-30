<?php

declare(strict_types=1);

namespace App\Cloud\Admin;

use App\System\Service\Config;
use Dux\Package\Package;
use Dux\Package\Update;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;

#[Resource(app: 'admin',  route: '/cloud/apps', name: 'cloud.apps', actions: false)]
class Apps
{

    #[Action(methods: 'GET', route: '')]
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $file = base_path('app.json');
        $config = Package::getJson($file);
        $apps = implode(',', array_keys($config['apps']));
        $auth = Package::getKey();
        $info = Package::app($auth[0], $auth[1], $apps);
        $auth = Package::getKey();
        $list = $info['apps'] ?: [];
        foreach ($list as $key => $vo) {
            $list[$key]['local_time'] = $config['apps'][$vo['name']];
            $list[$key]['update'] = $config['apps'][$vo['name']] < $vo['time'];
        }

        return send($response, 'ok', $list, [
            'auth' => $auth ?: []
        ]);
    }

    #[Action(methods: 'PUT', route: '')]
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $name = $data['name'];
        $output = new BufferedOutput();
        $auth = Package::getKey();
        Update::main($output, $auth[0], $auth[1], $name);
        return send($response, 'ok', [
            'content' => $output->fetch()
        ]);
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
