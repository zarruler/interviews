<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/11/19
 * Time: 11:13 PM
 */

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