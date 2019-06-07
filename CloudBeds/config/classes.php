<?php
use Twig\Environment;
use Core\Model;
use Psr\Container\ContainerInterface;

return [
    Environment::class => function () {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../App/Views');
        return new \Twig\Environment($loader);
    },
/*
    Model::class => function (ContainerInterface $container){
        $model = new Model();
        $model->setContainer($container)->getConnection();
        return $model;
    }
*/
];