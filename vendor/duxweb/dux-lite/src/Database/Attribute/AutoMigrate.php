<?php
declare(strict_types=1);

namespace Dux\Database\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AutoMigrate {

    public function __construct() {
    }
}