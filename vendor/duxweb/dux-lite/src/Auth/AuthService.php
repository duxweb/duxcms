<?php

namespace Dux\Auth;

use Dux\App;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthService
{

    public function __construct(public string $app)
    {
    }

    public function user(Request $request): ?object
    {
        $jwtStr = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        try {
            $jwt = JWT::decode($jwtStr, new Key(App::config("use")->get("app.secret"), 'HS256'));

        } catch (Exception $e) {
            return null;
        }
        if (!$jwt->sub || !$jwt->id) {
            return null;
        }
        if ($jwt->sub !== $this->app) {
            return null;
        }
        return $jwt;
    }

    public function check(Request $request): bool
    {
        return $this->user($request) !== null;
    }

    public function id(Request $request): int
    {
        return (int)$this->user($request)?->id;
    }
}