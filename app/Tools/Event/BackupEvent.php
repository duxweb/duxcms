<?php

namespace App\Tools\Event;

use Symfony\Contracts\EventDispatcher\Event;

class BackupEvent extends Event
{

    public array $data = [];

    public function __construct()
    {
    }

    public function set(string $name, string $model): void
    {
        $this->data[$name] = [
            'name' => $name,
            'model' => $model,
        ];
    }

    public function get(): array
    {
        return $this->data;
    }

}