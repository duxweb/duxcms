<?php
declare(strict_types=1);

namespace Dux\Route\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class RouteGroup {

    /**
     * @param string $app 路由注册名
     * @param string $pattern 路由前缀
     * @param array $middleware 中间件
     */
    public function __construct(
        public string $app,
        public string $pattern,
        public array $middleware = [],
    ) {}


}