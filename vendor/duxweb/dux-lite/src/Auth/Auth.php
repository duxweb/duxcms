<?php
declare(strict_types=1);

namespace Dux\Auth;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class Auth {

    public static function token(string $app, $params = [], int $expire = 86400): string {
        $time = time();
        $payload = [
            'sub' => $app,
            'iat' => $time,
            'exp' => $time + $expire,
        ];
        $payload = [...$payload, ...$params];
        return JWT::encode($payload, \Dux\App::config("use")->get("app.secret"), 'HS256');
    }
}