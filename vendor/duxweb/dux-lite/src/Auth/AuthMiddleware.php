<?php
declare(strict_types=1);

namespace Dux\Auth;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware {
    public function __construct(
        public string $app,
        public int $renewal = 43200,
        public bool $must = true,
    ) {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response {


        if (!$this->must) {
            $auth = $request->getHeaderLine('Authorization');
            if (!$auth) {
                return $handler->handle($request);
            }
        }

        $secret = \Dux\App::config("use")->get("app.secret");
        $renewal = $this->renewal;
        $app = $this->app;
        $jwt = new \Tuupola\Middleware\JwtAuthentication([
            "secret" => $secret,
            "secure" => false,
            "before" => function ($request, $arguments) use($app) {
                $token = $arguments["decoded"];
                return $request->withAttribute('auth', $token)->withAttribute('app', $app);
            },
            "after" => function ($response, $arguments) use ($renewal, $secret, $app) {
                $token = $arguments["decoded"];
                if ($app != $token["sub"]) {
                    throw new \Dux\Handlers\ExceptionBusiness("Authorization app error", 401);
                }
                $renewalTime = $token["iat"] + $renewal;
                $expire =  $token["exp"] - $token["iat"];
                $time = time();
                if ($renewalTime <= $time) {
                    $token["exp"] = $time + $expire;
                    $auth = JWT::encode($token, $secret, 'HS256');
                    return $response->withHeader("Authorization", "Bearer $auth");
                }
                return $response;
            },
            "error" => function ($response, $arguments) {
                throw new \Dux\Handlers\ExceptionBusiness($arguments["message"], 401);
            }
        ]);
        return $jwt->process($request, $handler);
    }
}