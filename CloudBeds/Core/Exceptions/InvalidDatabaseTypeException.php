<?php

namespace Core\Exceptions;

use Exception;

class InvalidDatabaseTypeException extends Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct(
            $message ?? 'Unknown database type'
        );
    }

}