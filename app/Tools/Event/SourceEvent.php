<?php

namespace App\Tools\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SourceEvent extends Event
{

    public array $data = [];

    public function __construct()
    {
    }

    public function set(string $label, string $route, string $model): void
    {
        $this->data[] = [
            'label' => $label,
            'route' => $route,
            'model' => $model
        ];
    }

    public function get(): array
    {
        return $this->data;
    }

}