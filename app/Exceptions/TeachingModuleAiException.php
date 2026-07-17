<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class TeachingModuleAiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $httpStatus = 502,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
