<?php
declare(strict_types=1);

namespace Dux\Resources;

use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;
use Dux\Bootstrap;
use Dux\Resources\Attribute\Action;
use Dux\Resources\Attribute\Resource;
use Exception;

class Register
{


    public array $app = [];
    public array $path = [];

    /**
     * 设置资源
     * @param string $name
     * @param \Dux\Resources\Resource $resource
     * @return void
     */
    public function set(string $name, \Dux\Resources\Resource $resource): void
    {
        $this->app[$name] = $resource;
    }

    /**
     * 获取资源
     * @param string $name
     * @return \Dux\Resources\Resource
     */
    public function get(string $name): \Dux\Resources\Resource
    {

        if (!isset($this->app[$name])) {
            throw new \Dux\Handlers\Exception("The resources app [$name] is not registered");
        }
        return $this->app[$name];
    }


    /**
     * 注解路由注册
     * @param Bootstrap $bootstrap
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function registerAttribute(Bootstrap $bootstrap): void
    {
        $attributes = (array)App::di()->get("attributes");

        $permission = $bootstrap->getPermission();
        $appMaps = [];
        $routeMaps = [];
        $permissionMaps = [];

        foreach ($attributes as $attribute => $list) {
            if (
                $attribute != Resource::class
            ) {
                continue;
            }
            foreach ($list as $vo) {
                $params = $vo["params"];
                $class = $vo["class"];
                [$className, $methodName, $name] = $this->formatFile($class);
                $params['auth'] = (bool)($params['auth'] ?? true);
                $params['can'] = (bool)($params['can'] ?? true);

                $middleware = $this->getMiddleware($params['app'], $params['auth'], $params['middleware']);

                $group = $bootstrap->route->get($params["app"])->resources(
                    pattern: $params["route"],
                    class: $class,
                    name: $params["name"] ?: $name,
                    actions: $params["actions"] ?? [],
                    softDelete: (bool)$params['softDelete'],
                    middleware: $middleware
                );
                $appMaps[$className] = $params['app'];
                $routeMaps[$className] = $group;
                if ($params['name'] && $params['auth'] && $params['can']) {
                    $permissionMaps[$className] = $permission->get($params['app'])->resources($params['name'], 0, $params['actions'] ?? [], (bool)$params['softDelete']);
                }
            }
        }

        foreach ($attributes as $attribute => $list) {
            if (
                $attribute != Action::class
            ) {
                continue;
            }
            foreach ($list as $vo) {
                $params = $vo["params"];
                $class = $vo["class"];
                [$className, $methodName, $name] = $this->formatFile($class);
                if (!isset($routeMaps[$className])) {
                    continue;
                }
                $route = $routeMaps[$className];
                $name = $name . "." . ($params["name"] ?: lcfirst($methodName));
                $appName = $appMaps[$className];

                $middleware = $params["middleware"] ?: [];
                if ($appName && $params['auth']) {
                    $middleware = $this->getMiddleware($appName, (bool)$params['auth'], $params['middleware']);
                }
                $route->map(
                    methods: is_array($params["methods"]) ? $params["methods"] : [$params["methods"]],
                    pattern: $params["route"],
                    callable: $class,
                    name: $name,
                    middleware: $middleware
                );
                if ($permissionMaps[$className] && (!isset($params['auth']) || $params['auth']) && (!isset($params['can']) || $params['can'])) {
                    if ($params["name"]) {
                        $permissionMaps[$className]->add($name, false);
                    }else {
                        $permissionMaps[$className]->add(lcfirst($methodName));
                    }
                }

            }
        }
    }

    private function getMiddleware(string $app, bool $auth, ?array $middleware = []): array
    {
        $resource = $this->get($app);
        if ($auth) {
            $data = $resource->getAllMiddleware();
        } else {
            $data = $resource->getMiddleware();
        }
        if ($middleware) {
            $data = [...$middleware, ...$middleware];
        }
        return array_filter($data);
    }

    private function formatFile($class): array
    {
        [$className, $methodName] = explode(":", $class, 2);
        $classArr = explode("\\", $className);
        $layout = array_slice($classArr, -3, 1)[0];
        $name = lcfirst($layout) . "." . lcfirst(end($classArr));

        return [$className, $methodName, $name];
    }

}