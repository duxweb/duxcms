<?php
declare(strict_types=1);

namespace Dux\Handlers;

/**
 * ExceptionBusiness
 */
class ExceptionNotFound  extends Exception {

    public function __construct() {
        parent::__construct("Not Found", 404);
    }

}