<?php

namespace Dux\Database;

use Symfony\Contracts\EventDispatcher\Event;

class DatabaseEvent extends Event
{

    private array $data = [];

    public function __construct()
    {
    }

    public function retrieved(callable $fun): void
    {
        $this->register('retrieved', $fun);
    }

    public function saving(callable $fun): void
    {
        $this->register('saving', $fun);
    }

    public function saved(callable $fun): void
    {
        $this->register('saved', $fun);
    }

    public function updating(callable $fun): void
    {
        $this->register('updating', $fun);
    }

    public function updated(callable $fun): void
    {
        $this->register('updated', $fun);
    }

    public function creating(callable $fun): void
    {
        $this->register('creating', $fun);
    }

    public function created(callable $fun): void
    {
        $this->register('created', $fun);
    }

    public function replicating(callable $fun): void
    {
        $this->register('replicating', $fun);
    }

    public function deleting(callable $fun): void
    {
        $this->register('deleting', $fun);
    }

    public function deleted(callable $fun): void
    {
        $this->register('deleted', $fun);
    }

    public function migration(callable $fun): void
    {
        $this->register('migration', $fun);
    }

    private function register(string $name, callable $fun): void
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = [];
        }
        $this->data[$name][] = $fun;
    }

    public function run(string $name, $info = null): void
    {
        if (!$this->data[$name]) {
            return;
        }
        foreach ($this->data[$name] as $vo) {
            $vo($info);
        }
    }
}