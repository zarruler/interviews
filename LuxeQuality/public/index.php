<?php
require_once '../vendor/autoload.php';
require_once '../app/Helper.php';

use App\Classes\Commands\CommandFactory;

$factory = new CommandFactory();

try {
    $factory->factory($argv[1], $argv[2])->execute();
} catch (Exception $e) {
    echo $e->getMessage()."\n";
}


