<?php
declare(strict_types=1);

namespace App\Tools\Config;

use App\Tools\Service\Magic;
use Dux\Bootstrap;
use Dux\Menu\MenuApp;
use Dux\Menu\MenuGroup;

class Menu
{

    static function Admin(\Dux\Menu\Menu $menu, Bootstrap $bootstrap): void
    {

        $data = Magic::getMenu($bootstrap);
        self::magicMenuGen($menu, $data);

    }

    static function magicMenuGen(\Dux\Menu\Menu $menu, ?array $data, MenuGroup|MenuApp $app = null): void
    {
        foreach ($data as $item) {
            if (!$app) {
                if (!$item['res']) {
                    $group = $menu->add("tools.data." . $item['name'], [
                        "label" => $item['label'],
                        "icon" => $item['icon'],
                        "meta" => [
                            "sort" => $item['sort'],
                        ]
                    ]);
                } else {
                    $group = $menu->add($item['res'], []);
                }
            } else {
                $group = $app->group("tools.data." . $item['name'], $item['icon'] ?: '', $item['sort'] ?: 0, $item['label']);
            }
            if (isset($item['children'])) {
                self::magicMenuGen($menu, $item['children'], $group);
            } else {
                $app->item(name: "tools.data." . $item['name'], route: "data/" . $item['name'], label: $item['label']);
            }
        }

    }

}