<?php
declare(strict_types=1);

namespace Dux\Handlers;

use Throwable;

/**
 * ExceptionBusiness
 */
class ExceptionData  extends Exception {
    public array $data;
}