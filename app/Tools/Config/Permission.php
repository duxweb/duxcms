<?php
declare(strict_types=1);

namespace App\Tools\Config;

use App\Tools\Service\Magic;
use Dux\Bootstrap;

class Permission
{

    static function Admin(\Dux\Permission\Permission $permission, Bootstrap $bootstrap): void
    {
        $data = Magic::getMenu($bootstrap);
        foreach ($data as $vo) {
            $group = $permission->group("tools.data." . $vo['name'])->label($vo['label']);
            foreach ($vo['children'] as $item) {
                $group->add($item['name'])->label($item['label']);
            }
        }

    }

}