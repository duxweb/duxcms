<?php
declare(strict_types=1);

namespace Dux\Permission;

class PermissionItem
{
    private string $label = '';

    public function __construct(public string $name)
    {

    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function get(): array
    {
        $labelData = explode(".", $this->name);
        $label = last($labelData);

        if (in_array($label, Permission::$actions)) {
            $label = __(  "resources.$label", "common");
        }else {
            $label = __( $this->name . ".name", 'manage');
        }

        return [
            "label" => $this->label ?: $label,
            "name" => $this->name,
        ];
    }

    public function getData(): string
    {
        return $this->name;
    }
}