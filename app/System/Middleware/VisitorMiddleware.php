<?php

namespace App\System\Middleware;

use App\System\App;
use App\System\Models\LogApi;
use App\System\Service\Visitor;
use Dux\Handlers\ExceptionBusiness;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class VisitorMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {
        $url = $request->getUri();
        $path = $url->getPath();

        if (!is_file(data_path('/install.lock')) && !str_contains($path, '/install')) {
            $res = new \Slim\Psr7\Response(302);
            return $res->withHeader('Location', '/install')
                ->withStatus(301);
        }else {
            Visitor::increment($request, 'common');
        }

        return $handler->handle($request);
    }
}