<?php
declare(strict_types=1);

namespace Dux\Resources;

use Dux\Bootstrap;
use Dux\Permission\Permission;
use Dux\Route\Route;

class Resource
{
    private array $authMiddleware = [];
    private array $middleware = [];

    public function __construct(public string $name, public string $route)
    {
    }

    public function addMiddleware(object ...$middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function addAuthMiddleware(object ...$middleware): self
    {
        $this->authMiddleware = $middleware;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getAuthMiddleware(): array
    {
        return $this->authMiddleware;
    }


    public function getAllMiddleware(): array
    {
        return array_filter([...$this->middleware, ...$this->authMiddleware]);
    }

    public function run(Bootstrap $bootstrap): void
    {
        $bootstrap->getPermission()->set($this->name, new Permission());
        $bootstrap->getRoute()->set($this->name, new Route($this->route));
    }
}