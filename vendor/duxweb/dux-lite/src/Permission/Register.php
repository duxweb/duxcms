<?php
declare(strict_types=1);

namespace Dux\Permission;

use Dux\Handlers\Exception;

class Register
{

    public array $app = [];

    /**
     * 设置权限应用
     * @param string $name
     * @param Permission $permission
     * @return void
     */
    public function set(string $name, Permission $permission): void
    {
        $permission->setApp($name);
        $this->app[$name] = $permission;
    }

    /**
     * 获取路由应用
     * @param string $name
     * @return Permission
     */
    public function get(string $name): Permission
    {

        if (!isset($this->app[$name])) {
            throw new Exception("The menu permission [$name] is not registered");
        }
        return $this->app[$name];
    }

}