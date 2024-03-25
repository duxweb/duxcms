<?php

namespace Dux\Api;

use Dux\App;
use Symfony\Contracts\EventDispatcher\Event;

class ApiEvent extends Event
{
    private array $data = [];

    public function __construct()
    {
    }

    public function register(string $name, callable $fun): void
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = [];
        }
        $this->data[$name][] = $fun;
    }

    public function get(string $name, ...$params): array
    {
        if (!$this->data[$name]) {
            return [];
        }
        $data = [];
        foreach ($this->data[$name] as $vo) {
            $tmp = $vo(...$params);
            if ($tmp) {
                $data = [...$data, ...$tmp];
            }
        }
        return $data;
    }

    public function run(string $name, ...$params): void
    {
        if (!$this->data[$name]) {
            return;
        }
        foreach ($this->data[$name] as $vo) {
            $vo(...$params);
        }
    }

}