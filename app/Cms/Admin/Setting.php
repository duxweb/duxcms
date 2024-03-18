<?php

declare(strict_types=1);

namespace App\Cms\Admin;

use App\System\Models\SystemUser;
use App\System\Service\Config;
use Dux\Handlers\ExceptionBusiness;
use Dux\Package\Install;
use Dux\Package\Package;
use Dux\Package\Uninstall;
use Dux\Package\Update;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Output\BufferedOutput;

#[Resource(app: 'admin', route: '/cms/setting', name: 'cms.setting', actions: false)]
class Setting
{

    #[Action(methods: 'GET', route: '')]
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = [
            'site' => Config::getJsonValue('site'),
            'cms' => Config::getJsonValue('cms')
        ];
        return send($response, 'ok', $data);
    }

    #[Action(methods: 'PUT', route: '')]
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        foreach ($data as $key => $vo) {
            Config::setValue($key, $vo);
        }

        return send($response, __('message.edit', 'common'));
    }


}
