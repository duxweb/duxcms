<?php
declare(strict_types=1);

namespace App\Tools\Config;

use App\Tools\Service\Magic;
use Dux\Bootstrap;

class Menu
{

    static function Admin(\Dux\Menu\Menu $menu, Bootstrap $bootstrap): void
    {
        $app = $menu->add("data", [
            "label" => "data",
            "icon" => 'i-tabler:database',
            "meta" => [
                "sort" => 100,
            ]
        ]);

        $data = Magic::getMenu($bootstrap);
        foreach ($data as $vo) {
            $group = $app->group("tools.data." . $vo['name'], $vo['icon'], 80, $vo['label']);
            foreach ($vo['children'] as $item) {
                $group->item(name: "tools.data." . $vo['name'] . '.' . $item['name'], route: "data/" . $item['name'], label: $item['label']);
            }
        }

    }

}