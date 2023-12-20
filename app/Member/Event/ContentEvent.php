<?php

namespace App\Member\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ContentEvent extends Event
{
    public array $maps = [];

    public function __construct()
    {
    }

    public function setMap(string $name, string $type): void
    {
        $this->maps[$name] = $type;
    }

    public function getMapName(string $type): string
    {
        foreach ($this->maps as $name => $vo) {
            if ($vo == $type) {
                return $name;
            }
        }
        return '';
    }

    public function getMapType(string $name): string
    {
        return $this->maps[$name];
    }

}