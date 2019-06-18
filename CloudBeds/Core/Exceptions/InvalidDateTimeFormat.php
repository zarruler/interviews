<?php

namespace Core\Exceptions;

use Exception;

class InvalidDateTimeFormat extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct(
            !empty($message) ? $message : 'Invalid DateType format'
        );
    }
}