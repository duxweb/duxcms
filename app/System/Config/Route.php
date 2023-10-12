<?php

declare(strict_types=1);

namespace App\System\Config;

use App\System\Admin\Auth;
use App\System\Admin\Depart;
use App\System\Admin\Operate;
use App\System\Admin\Role;
use App\System\Admin\UserBF;
use Dux\App;
use Dux\Route\Route as DuxRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route
{

	static function AuthAdmin(DuxRoute $route): void
	{
        $route->get("/notify", \App\System\Admin\Notify::class.":get", "system.notify.get", "通知事件");
        $route->get(pattern: "/personage", callable: UserBF::class.":personage", name: "system.personage.info", title: "个人信息");
        $route->post(pattern: "/personage", callable: UserBF::class.":personageSave", name: "system.personage.save", title: "个人保存");
        $route->get(pattern: "/personage/login", callable: UserBF::class.":personageLogin", name: "system.personage.login", title: "登录日志");
        $route->get(pattern: "/personage/operate", callable: UserBF::class.":personageOperate", name: "system.personage.operate", title: "操作日志");
		$route->manage(pattern: "/system/role", class: Role::class, name: "system.role", title: "角色");
		$route->manage(pattern: "/system/depart", class: Depart::class, name: "system.depart", title: "部门");
		$route->manage(pattern: "/system/api", class: \App\System\Admin\Api::class, name: "system.api", title: "接口授权");
        $route->get("/system/operate", Operate::class.":list", "system.operate", "操作记录");
        $route->get("/system/app/label", \App\System\Admin\App::class.":label", "system.app.label", "应用标签");
	}


    static function Api(DuxRoute $route): void
    {
        $route->get('/test', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
            return send($response, 'dddd');
        }, 'test');
    }
}
