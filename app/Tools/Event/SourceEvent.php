<?php

namespace App\Tools\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SourceEvent extends Event
{

    public array $data = [];

    public function __construct()
    {
    }

    public function set(string $name, string $label, string $route, callable $data, callable $format = null): void
    {
        $this->data[$name] = [
            'name' => $name,
            'label' => $label,
            'route' => $route,
            'data' => $data,
            'format' => $format
        ];
    }

    public function get(): array
    {
        return $this->data;
    }

}