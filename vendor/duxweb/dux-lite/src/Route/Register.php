<?php
declare(strict_types=1);

namespace Dux\Route;

use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;
use Dux\Bootstrap;
use Dux\Handlers\Exception;
use Dux\Route\Attribute\RouteGroup;

class Register
{

    public array $app = [];
    public array $path = [];

    /**
     * 设置路由应用
     * @param string $name
     * @param Route $route
     * @return void
     */
    public function set(string $name, Route $route): void
    {
        $route->setApp($name);
        $this->app[$name] = $route;
    }

    /**
     * 获取路由应用
     * @param string $name
     * @return Route
     */
    public function get(string $name): Route
    {

        if (!isset($this->app[$name])) {
            throw new Exception("The routing app [$name] is not registered");
        }
        return $this->app[$name];
    }

    /**
     * 注解路由注册
     * @param Bootstrap $bootstrap
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function registerAttribute(Bootstrap $bootstrap): void
    {
        $attributes = (array)App::di()->get("attributes");

        $permission = $bootstrap->getPermission();
        $groupClass = [];
        $permissionClass = [];

        foreach ($attributes as $attribute => $list) {
            if (
                $attribute != RouteGroup::class
            ) {
                continue;
            }
            foreach ($list as $vo) {
                $params = $vo["params"];
                $class = $vo["class"];
                [$className, $methodName, $name] = $this->formatFile($class);
                $group = $this->get($params["app"])->group($params["pattern"], ...($params["middleware"] ?? []));
                $groupClass[$className] = $group;
            }
        }

        foreach ($attributes as $attribute => $list) {
            if (
                $attribute != \Dux\Route\Attribute\Route::class
            ) {
                continue;
            }
            foreach ($list as $vo) {
                $params = $vo["params"];
                $class = $vo["class"];
                [$className, $methodName, $name] = $this->formatFile($class);
                // route
                if (str_contains($class, ":")) {
                    // method
                    if (!$params["app"] && !isset($groupClass[$className])) {
                        continue;
                    }
                    $group = $params["app"] ? $this->get($params["app"]) : $groupClass[$className];
                } else {
                    // class
                    if (empty($params["app"])) {
                        throw new \Exception("class [" . $class . "] route attribute parameter missing \"app\" ");
                    }
                    $group = $this->get($params["app"]);
                }
                $name = $params["name"] ?: $name . ($methodName ? "." . lcfirst($methodName) : "");
                $group->map(
                    methods: is_array($params["methods"]) ? $params["methods"] : [$params["methods"]],
                    pattern: $params["pattern"] ?: '',
                    callable: $class,
                    name: $name
                );

            }
        }
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