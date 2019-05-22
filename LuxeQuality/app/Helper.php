<?php
if (! function_exists('__')) {
    function __($key, $value)
    {
        $fileName = '../app/Resources/' . $key . '.php';
        if(!is_file($fileName))
            throw new Exception("Failed to include {$key} resource");

        $resource = require $fileName;

        if(!isset($resource[$value]))
            throw new Exception("Failed to find resource key \"{$key}.{$value}\"");

        return $resource[$value];
    }
}