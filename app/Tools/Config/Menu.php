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
            "sort" => 800,
            "label" => "data"
        ]);

        $data = Magic::get($bootstrap);
        foreach ($data as $vo) {
            $group = $app->group("data." . $vo['name'], $vo['icon'], 80, $vo['label']);
            foreach ($vo['children'] as $item) {
                $group->item(name: "data." . $item['name'], route: "data/" . $item['name'], label: $item['label']);
            }
        }

    }

}