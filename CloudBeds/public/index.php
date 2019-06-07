<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$builder = new DI\ContainerBuilder();

$builder->addDefinitions(dirname(__DIR__) . '/config/db.php');
$builder->addDefinitions(dirname(__DIR__) . '/config/routes.php');
$builder->addDefinitions(dirname(__DIR__) . '/config/classes.php');

$container = $builder->build();
$container->set('Symfony\\Component\\HttpFoundation\\Request', \DI\Factory(function () {
    return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
}));

$container->get('\Core\App');
