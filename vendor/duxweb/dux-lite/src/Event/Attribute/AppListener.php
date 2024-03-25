<?php
declare(strict_types=1);

namespace Dux\Event\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class AppListener {

    public function __construct(string $name, string $class, int $priority = 0) {
    }
}