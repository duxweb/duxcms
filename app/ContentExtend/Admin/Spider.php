<?php

declare(strict_types=1);

namespace App\ContentExtend\Admin;

use App\Content\Models\ArticleClass;
use App\System\Service\Config;
use App\Tools\Models\ToolsMagic;
use Dux\Handlers\ExceptionBusiness;
use Dux\Package\Package;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Dux\Utils\Content;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin', route: '/contentExtend/spider', name: 'contentExtend.spider', actions: false)]
class Spider extends Resources
{
    #[Action(methods: 'POST', route: '/content')]
    public function extract(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?: [];

        $auth = Package::getKey();
        if (!$auth) {
            throw new ExceptionBusiness(__('contentExtend.spider.validator.user', 'manage'));
        }
        $uri = $data['url'];
        if (!$uri) {
            throw new ExceptionBusiness(__('contentExtend.spider.validator.uri', 'manage'));

        }

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0',
            'Referer' => $uri
        ];

        $client = new Client();
        $result = $client->request('get', $uri, [
            'headers' => $headers
        ]);
        try {
            $content = $result?->getBody()?->getContents();
        }catch (\Exception $e) {
            throw new ExceptionBusiness(__('contentExtend.spider.validator.notFound', 'manage'));
        }
        $resultData = Package::request('post', '/v/services/extract', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $auth
            ],
            'json' => [
                'content' => $content,
                'uri' => $uri
            ]
        ]);

        return send($response, 'ok', $resultData);
    }

}
