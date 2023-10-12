<?php

namespace App\System\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AppEvent extends Event
{

    private array $label = [];

    public function label(array $item): void
    {
        $this->label[] = $item;
    }

    public function getLabel(): array
    {
        return $this->label;
    }

}