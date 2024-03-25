<?php

namespace Dux\Permission;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Psr\Http\Message\ServerRequestInterface;

class Can
{
    private static array $permission = [];

    public static function check(ServerRequestInterface $request, string $model, string $name): void
    {
        $auth = $request->getAttribute("auth");
        $uid = $auth['id'];

        $allPermission = App::permission($auth['sub'])->getData();
        if (!$allPermission || !in_array($name, $allPermission)) {
            return;
        }
        if (!self::$permission) {
            $userInfo = (new $model)->query()->find($uid);
            self::$permission = $userInfo->permission;
        }
        if (self::$permission && !self::$permission[$name]) {
            throw new ExceptionBusiness('The user does not have permission', 403);
        }
    }

}