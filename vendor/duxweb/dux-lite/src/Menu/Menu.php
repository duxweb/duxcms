<?php
declare(strict_types=1);

namespace Dux\Menu;

class Menu
{

    private array $data = [];
    private array $push = [];


    public function __construct(public string $prefix = '')
    {
    }

    public function add(string $name, array $config): MenuApp
    {
        $config["name"] = $name;
        $menuApp = new MenuApp($name, $config, $this->prefix);
        $this->data[$name] = $menuApp;
        return $menuApp;
    }

    public function push(string $name): MenuApp
    {
        $menuApp = new MenuApp($name);
        $this->push[$name][] = $menuApp;
        return $menuApp;
    }

    public function get(): array
    {
        $menuData = [];
        foreach ($this->data as $name => $app) {
            $appData = $app->get();
            if ($this->push[$name]) {
                foreach ($this->push[$name] as $push) {
                    $object = $push->get();
                    $appData["children"] = [...$appData["children"], ...($object["children"] ?: [])];
                }
            }
            $menuData[] = $appData;
        }
        $restData = [];
        foreach ($menuData as $appData) {
            $groupsMenu = [];
            foreach ($appData["children"] as $groupData) {
                $list = [];
                foreach ($groupData["children"] as $vo) {
                    $list[] = $vo;
                }
                $list = collect($list)->sortBy('sort')->values()->toArray();
                if (!$list && !$groupData['route']) {
                    continue;
                }
                $groupData["children"] = $list;
                $groupsMenu[] = $groupData;
            }
            $groupList = collect($groupsMenu)->sortBy('sort')->values()->toArray();
            if (empty($groupList) && !$appData['route']) {
                continue;
            }
            $appData["children"] = $groupList;
            $restData[] = $appData;
        }
        return collect($restData)->sortBy('sort')->values()->toArray();

    }

}