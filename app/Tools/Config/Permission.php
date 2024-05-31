<?php
declare(strict_types=1);

namespace App\Tools\Config;

use App\Tools\Service\Magic;
use Dux\Bootstrap;
use Dux\Menu\MenuApp;
use Dux\Menu\MenuGroup;
use Dux\Permission\PermissionGroup;

class Permission
{

    static function Admin(\Dux\Permission\Permission $permission, Bootstrap $bootstrap): void
    {
        $data = Magic::getMenu($bootstrap);
        $group = $permission->group("tools.magicData");
        self::magicPermissionGen($data, $group);
    }

    static function magicPermissionGen( ?array $data, \Dux\Permission\Permission|PermissionGroup $permission): void
    {
        foreach ($data as $vo) {
            if (!isset($vo['children'])) {
                $permission->add("tools.data." . $vo['name'], false)->label($vo['label']);
            } else {
                self::magicPermissionGen($vo['children'], $permission);
            }
        }

    }

}