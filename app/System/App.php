<?php
declare(strict_types=1);

namespace App\System;

use App\System\Middleware\OperateMiddleware;
use App\System\Middleware\VisitorMiddleware;
use App\System\Models\SystemApi;
use App\System\Models\SystemUser;
use App\Tools\Handlers\Upload;
use Dux\Api\ApiMiddleware;
use Dux\App\AppExtend;
use Dux\Auth\AuthMiddleware;
use Dux\Bootstrap;
use Dux\Handlers\ExceptionBusiness;
use Dux\Menu\Menu as DuxMenu;
use Dux\Permission\PermissionMiddleware;
use Dux\Resources\Resource as DuxResource;
use Dux\Route\Route as DuxRoute;

class App extends AppExtend
{
    public function init(Bootstrap $app): void
    {
        // 初始化资源
        $app->getResource()->set(
            "admin",
            (new DuxResource(
                'admin',
                '/admin'
            ))->addAuthMiddleware(
                new OperateMiddleware(SystemUser::class),
                new PermissionMiddleware("admin", SystemUser::class),
                new AuthMiddleware("admin")
            )
        );

        // 初始化路由
        $app->getRoute()->set("web", new DuxRoute("", new VisitorMiddleware()));

        $app->getRoute()->set("api",
            new DuxRoute("/api",
                new ApiMiddleware(function ($id) {
                    $apiInfo = SystemApi::query()->where('secret_id', $id)->firstOr(function () {
                        throw new ExceptionBusiness('Signature authorization failed', 402);
                    });
                    return $apiInfo->secret_key;
                })
            ),
        );
        // 初始化菜单
        $app->getMenu()->set("admin", new DuxMenu('/admin'));

    }

    public function register(Bootstrap $app): void
    {
        $middleware = $app->getResource()->get("admin")->getAllMiddleware();

        $commonRoute = $app->getRoute()->get("admin")->group('', ...$middleware);
        $commonRoute->post('/upload', Upload::class . ':upload', 'admin.upload');
        $commonRoute->post('/qiniu', Upload::class . ':qiniu', 'admin.qiniu');
        $commonRoute->map(['GET', 'POST', 'DELETE'],'/upload/manage', Upload::class . ':manage', 'admin.upload.manage');
        $commonRoute->post('/upload/remote', Upload::class . ':remote', 'admin.upload.remote');
    }


}