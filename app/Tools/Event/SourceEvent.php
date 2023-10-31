<?php

namespace App\Tools\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SourceEvent extends Event
{

    public array $data = [];

    public function __construct()
    {
    }

    public function set(string $name, string $label, string $route, callable $data): void
    {
        $this->data[$name] = [
            'name' => $name,
            'label' => $label,
            'route' => $route,
            'data' => $data
        ];
    }

    public function get(): array
    {
        return $this->data;
    }

}