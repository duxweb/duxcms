<?php
declare(strict_types=1);

namespace Dux\Handlers;

use Throwable;

/**
 * ExceptionValidator
 */
class ExceptionValidator  extends ExceptionData {

    public function __construct(array $data) {
        $errors = array_values($data);
        $message = $errors[0] ? $errors[0][0] : '';
        parent::__construct($message, 422);
        $this->data = $data;
    }
}