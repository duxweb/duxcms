<?php
declare(strict_types=1);

namespace Dux\Menu;

class MenuItem {

    public function __construct(public string $groupName, public string $name, public string $route, public string $icon = '', public int $sort = 0, public string $label = '', public string $prefix = '') {
    }

    public function get(): array {
        return [
            "key" => $this->groupName . '/' .$this->name,
            "name" => $this->name,
            "label" => __($this->label ?: $this->name . '.name', 'manage'),
            "icon" => $this->icon,
            "route" => $this->prefix . '/' . $this->route,
            "sort" => $this->sort,
        ];
    }
}