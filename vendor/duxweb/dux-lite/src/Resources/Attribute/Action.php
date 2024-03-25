<?php
declare(strict_types=1);

namespace Dux\Resources\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Action
{
    /**
     * @param array|string $methods 请求方法
     * @param string $route 路由
     * @param string $name 资源名
     * @param bool $auth 授权，当资源为非授权时独立使用
     * @param bool $can 权限，当资源为非授权时独立使用
     */
    public function __construct(
        array|string $methods,
        string       $route,
        string       $name = '',
        ?bool        $auth = null,
        bool        $can = true,
    )
    {
    }
}