<?php

declare(strict_types=1);

namespace App\Member;

use App\Member\Models\MemberUser;
use App\System\Models\SystemApi;
use Closure;
use Dux\Api\ApiMiddleware;
use Dux\App\AppExtend;
use Dux\Auth\AuthMiddleware;
use Dux\Bootstrap;
use Dux\Handlers\ExceptionBusiness;
use Dux\Route\Route as DuxRoute;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Application Registration
 */
class App extends AppExtend
{

    public static function CheckAuth(): Closure
    {
        return function (Request $request, RequestHandler $handler) {
            $auth = $request->getAttribute('auth');
            $info = MemberUser::query()->where('id', $auth['id'])->exists();
            if (!$info) {
                throw new ExceptionBusiness("登录失效，请重新登录", 401);
            }
            return $handler->handle($request);
        };
    }

    public function init(Bootstrap $app): void
    {
        $app->getRoute()->set("apiMember",
            new DuxRoute("/api",
                new ApiMiddleware(function ($id) {
                    $apiInfo = SystemApi::query()->where('secret_id', $id)->firstOr(function () {
                        throw new ExceptionBusiness('Signature authorization failed', 402);
                    });
                    return $apiInfo->secret_key;
                }),
                self::CheckAuth(),
                new AuthMiddleware("member", 1728000)
            ),
        );
    }

    public function register(Bootstrap $app): void
    {
    }
}
