<?php

namespace App\Content\Api;

use Dux\Handlers\ExceptionNotFound;
use Dux\Route\Attribute\Route;
use Dux\Route\Attribute\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


#[RouteGroup(app: 'api', pattern: '/content/category')]
class Category
{

    #[Route(methods: 'GET', pattern: '')]
    public function class(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $query = \App\Content\Models\ArticleClass::query();
        if ($params['class']) {
            $query->where('parent_id', $params['class']);
        }
        $list = $query->get()->toTree();
        return send($response, 'ok', [
            'list' => $list->toArray() ?: [],
        ]);
    }


}