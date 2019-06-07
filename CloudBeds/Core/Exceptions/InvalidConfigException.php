<?php

namespace Core\Exceptions;

use Exception;

final class InvalidConfigException extends Exception
{
    const MESSAGE = '%s config has either invalid format or missing required keys';

    /**
     * @param string $configName
     * @return InvalidConfigException
     */
    public static function create(string $configName)
    {
        $message = sprintf(self::MESSAGE, $configName);
        return new self($message);
    }
}