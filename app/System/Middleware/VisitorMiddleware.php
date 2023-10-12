<?php

namespace App\System\Middleware;

use App\System\App;
use App\System\Models\LogApi;
use App\System\Service\Visitor;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class VisitorMiddleware {

    public function __invoke(Request $request, RequestHandler $handler): Response {

        Visitor::increment($request, 'common');
        $response = $handler->handle($request);
        return $response;
    }
}