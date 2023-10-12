<?php

declare(strict_types=1);

namespace App\Tools\Config;

use App\Tools\Handlers\Upload;
use Dux\Route\Route as DuxRoute;

class Route
{
    static function AuthAdmin(DuxRoute $route): void
    {
        $route->post("/tools/upload", Upload::class . ":upload", "tools.upload", "文件上传");
        $route->get("/tools/fileManage", Upload::class . ":manage", "tools.fileManage", "文件管理器");
        $route->get("/tools/uploadQiniu", Upload::class . ":qiniu", "tools.qiniu", "七牛上传");
    }
}
