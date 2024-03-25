<?php

namespace Dux\Resources;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ResourcesEvent extends Event
{

    private array $data = [];

    public function __construct()
    {
    }

    public function init(callable $fun): void
    {
        $this->register('init', $fun);
    }

    public function validator(callable $fun): void
    {
        $this->register('validator', $fun);
    }

    public function transform(callable $fun): void
    {
        $this->register('transform', $fun);
    }
    public function format(callable $fun): void
    {
        $this->register('format', $fun);
    }

    public function createBefore(callable $fun): void
    {
        $this->register('createBefore', $fun);
    }

    public function createAfter(callable $fun): void
    {
        $this->register('createAfter', $fun);
    }

    public function queryOne(callable $fun): void
    {
        $this->register('queryOne', $fun);
    }

    public function queryMany(callable $fun): void
    {
        $this->register('queryMany', $fun);
    }

    public function query(callable $fun): void
    {
        $this->register('query', $fun);
    }

    public function metaMany(callable $fun): void
    {
        $this->register('metaMany', $fun);
    }

    public function metaOne(callable $fun): void
    {
        $this->register('metaOne', $fun);
    }

    public function editBefore(callable $fun): void
    {
        $this->register('editBefore', $fun);
    }

    public function editAfter(callable $fun): void
    {
        $this->register('editAfter', $fun);
    }

    public function delBefore(callable $fun): void
    {
        $this->register('delBefore', $fun);
    }

    public function delAfter(callable $fun): void
    {
        $this->register('delAfter', $fun);
    }

    public function storeBefore(callable $fun): void
    {
        $this->register('storeBefore', $fun);
    }

    public function storeAfter(callable $fun): void
    {
        $this->register('storeAfter', $fun);
    }

    public function restoreBefore(callable $fun): void
    {
        $this->register('restoreBefore', $fun);
    }

    public function restoreAfter(callable $fun): void
    {
        $this->register('restoreAfter', $fun);
    }

    public function trashBefore(callable $fun): void
    {
        $this->register('trashBefore', $fun);
    }

    public function trashAfter(callable $fun): void
    {
        $this->register('trashAfter', $fun);
    }

    private function register(string $name, callable $fun): void
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