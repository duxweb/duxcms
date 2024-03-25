<?php
declare(strict_types=1);

namespace Dux\Menu;


class MenuApp extends \Dux\Bootstrap
{

    private array $data = [];

    public function __construct(public string $name, public array $config = [], public string $prefix = '')
    {
    }

    public function group(string $name, $icon = '', int $sort = 0, string $label = ''): MenuGroup
    {
        $app = new MenuGroup($this->name, $name, $icon, $sort, $label, $this->prefix);
        $this->data[] = $app;
        return $app;
    }

    public function item(string $name, string $route, string $icon = '', int $sort = 0, string $label = ''): MenuItem
    {
        $app = new MenuItem($this->name, $name, $route, $icon, $sort, $label, $this->prefix);
        $this->data[] = $app;
        return $app;
    }

    public function get(): array
    {
        $data = [];
        foreach ($this->data as $vo) {
            $data[] = $vo->get();
        }
        return [
            ...$this->config,
            ...[
                "name" => $this->name,
                "key" => '/' . $this->name,
                "label" =>  __($this->config['label'] ?: $this->name . '.name', 'manage'),
                "route" => ($this->config["route"] ? $this->prefix . '/' : '') . $this->config["route"],
                "sort" => $this->config["sort"] ?: 0,
                "children" => $data,
            ]
        ];
    }
}