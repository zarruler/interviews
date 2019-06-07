<?php

namespace Core;
use Core\Interfaces\ModelInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment as Twig;

class Controller
{
    protected $container;
    protected $request;
    /**
     * @var Twig
     */
    protected $twig;

    public function __construct(ContainerInterface $container, Request $request, Twig $twig)
    {
        $this->container = $container;
        $this->request = $request;
        $this->twig = $twig;
    }

    /**
     * @param string $modelName
     * @param string|NULL $namespace
     * @return ModelInterface
     */
    public function getModel(string $modelName, string $namespace = NULL) : ModelInterface
    {
        $namespace = $namespace ?? Model::MODEL_NAMESPACE;
        return $this->container->get($namespace.$modelName);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getTwig(): Twig
    {
        return $this->twig;
    }
}