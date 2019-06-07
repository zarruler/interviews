<?php

namespace Core\Route;

use Core\Config\RoutesConfig;
use Psr\Container\ContainerInterface;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;
use Core\Header;

class Router
{
    const HANDLER_DELIMITER = '@';
    /**
     * @var RoutesConfig
     */
    private $routesConfig;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Header
     */
    private $header;

    /**
     * @param RoutesConfig $routesConfig
     * @param Request $request
     * @param ContainerInterface $container
     * @param Header $header
     */
    public function __construct(
        RoutesConfig $routesConfig,
        Request $request,
        Header $header,
        ContainerInterface  $container
    )
    {
        $this->routesConfig = $routesConfig;
        $this->request = $request;
        $this->header = $header;
        $this->container = $container;
    }

    public function dispatchRoute()
    {
        $dispatcher = $this->getDispatcher();
        $request = $this->getRequest();
        $requestMethod = $request->server->get('REQUEST_METHOD');
        $requestUri = $request->server->get('REQUEST_URI');

        /** @noinspection ReturnFalseInspection */
        if (false !== $pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        $requestUri = rawurldecode($requestUri);

        $this->dispatch($requestMethod, $requestUri, $dispatcher);
    }

    /**
     * @param string $requestMethod
     * @param string $requestUri
     * @param Dispatcher $dispatcher
     */
    private function dispatch(string $requestMethod, string $requestUri, Dispatcher $dispatcher)
    {
        $routeInfo = $dispatcher->dispatch($requestMethod, $requestUri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $this->header->sendNotFound('Page not found');
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->header->sendMethodNotAllowed('Method not allowed');
                break;
            case Dispatcher::FOUND:
                list($state, $handler, $vars) = $routeInfo;
                list($class, $method) = explode(static::HANDLER_DELIMITER, $handler, 2);

                $controller = $this->getContainer()->get($class);
                $controller->{$method}(...array_values($vars));

                unset($state);
                break;
        }
    }

    /**
     * @return Dispatcher
     */
    private function getDispatcher(): Dispatcher
    {
        $routesConfig = $this->getRoutesConfig();
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $route) use ($routesConfig) {
            foreach ($routesConfig->createRouteGroupItems() as $routeGroupItem) {
                $route->addRoute(
                    $routeGroupItem->getHttpMethod(),
                    $routeGroupItem->getBase() . $routeGroupItem->getRoute(),
                    $routeGroupItem->getController() .
                    static::HANDLER_DELIMITER .
                    $routeGroupItem->getControllerAction()
                );
            }
        });

        return $dispatcher;
    }

    /**
     * @return RoutesConfig
     */
    private function getRoutesConfig(): RoutesConfig
    {
        return $this->routesConfig;
    }

    /**
     * @return Request
     */
    private function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}