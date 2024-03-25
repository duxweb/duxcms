<?php
declare(strict_types=1);

namespace Dux\Event\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Listener {

    public function __construct(string $name, int $priority = 0) {
    }
}