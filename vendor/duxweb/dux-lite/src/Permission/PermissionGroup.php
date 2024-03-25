<?php
declare(strict_types=1);

namespace Dux\Permission;

class PermissionGroup
{
    private array $data = [];

    public function __construct(public string $app, public string $name, public int $order, public string $label, public string $pattern = "")
    {
    }

    public function add(string $name, bool $complete = true): PermissionItem
    {
        $item = new PermissionItem($complete ? $this->pattern . $this->name . "." . $name : $name);
        $this->data[] = $item;
        return $item;
    }

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function get(): array
    {
        $children = [];
        foreach ($this->data as $vo) {
            $children[] = $vo->get();
        }
        return [
            "label" => $this->label ?: __($this->pattern . $this->name . ".name", 'manage'),
            "name" => "group:" . $this->pattern . $this->name,
            "order" => $this->order,
            "children" => $children,
        ];
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->data as $vo) {
            $data = [...$data, $vo->getData()];
        }
        return $data;
    }
}