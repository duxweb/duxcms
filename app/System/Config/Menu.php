<?php
declare(strict_types=1);

namespace App\System\Config;

use Dux\App;

class Menu
{
    static function Admin(\Dux\Menu\Menu $menu): void
    {

        $app = $menu->add("home", [
            "icon" => "home",
            "sort" => 0,
            'url' => "system/total/index",
        ]);


        $app = $menu->add("system", [
            "icon" => "setting",
            "sort" => 100,
        ]);

        $group = $app->group( "system.api", "api");
        $group->item("system.api.list", "system/api", 0);

        $group = $app->group("system.user", "user");
        $group->item("system.user", "system/user", 0);
        $group->item("system.role", "system/role", 1);


        $group = $app->group("system.log", "log");
        $group->item("system.operate", "system/operate", 0);

    }

}