<?php

namespace Core\Config;

use Core\Exceptions\InvalidConfigException;
use Generator;
use Core\Route\RouteGroupItem;
use Psr\Container\ContainerInterface;

class RoutesConfig
{
    const CONFIG_NAME = 'Routes';
    /**
     * @var string[]
     */
    private $routes;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Generator|RouteGroupItem[]
     * @throws InvalidConfigException
     */
    public function createRouteGroupItems(): Generator
    {
        $routes = $this->getRoutes();

        foreach ($routes as $groupRoute) {
            yield from $this->createRouteGroupItemsForGroup($groupRoute);
        }
    }

    /**
     * @param string[] $groupRoute
     * @return Generator|RouteGroupItem[]
     * @throws InvalidConfigException
     */
    private function createRouteGroupItemsForGroup(array $groupRoute)
    {
        if (!array_key_exists(RouteConfigKeys::GROUP_BASE, $groupRoute)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        if (!array_key_exists(RouteConfigKeys::GROUP_ITEMS, $groupRoute)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        $base = $groupRoute[RouteConfigKeys::GROUP_BASE];

        foreach ($groupRoute[RouteConfigKeys::GROUP_ITEMS] as $groupRouteItem) {
            $this->checkGroupRouteItemKeys($groupRouteItem);

            $route = $groupRouteItem[RouteConfigKeys::ROUTE];
            $httpMethod = $groupRouteItem[RouteConfigKeys::HTTP_METHOD];
            $controller = $groupRouteItem[RouteConfigKeys::CONTROLLER];
            $controllerAction = $groupRouteItem[RouteConfigKeys::CONTROLLER_ACTION];

            yield $this->createRouteGroupItem(
                $base,
                $route,
                $httpMethod,
                $controller,
                $controllerAction
            );
        }
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    private function getRoutes(): array
    {
        if (!$this->container->has(RouteConfigKeys::GROUP_ROUTES) || !is_array($this->container->get(RouteConfigKeys::GROUP_ROUTES))){
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        if (null === $this->routes) {
            $this->routes = $this->container->get(RouteConfigKeys::GROUP_ROUTES);
        }

        return $this->routes;
    }


    /**
     * @param string $base
     * @param string $route
     * @param string $httpMethod
     * @param string $controller
     * @param string $controllerAction
     * @return RouteGroupItem
     */
    private function createRouteGroupItem(
        string $base,
        string $route,
        string $httpMethod,
        string $controller,
        string $controllerAction
    ): RouteGroupItem
    {
        return new RouteGroupItem($base, $route, $httpMethod, $controller, $controllerAction);
    }

    /**
     * @param string[] $groupRouteItem
     * @throws InvalidConfigException
     */
    private function checkGroupRouteItemKeys(array $groupRouteItem)
    {
        if (!array_key_exists(RouteConfigKeys::ROUTE, $groupRouteItem)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        if (!array_key_exists(RouteConfigKeys::HTTP_METHOD, $groupRouteItem)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        if (!array_key_exists(RouteConfigKeys::CONTROLLER, $groupRouteItem)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }

        if (!array_key_exists(RouteConfigKeys::CONTROLLER_ACTION, $groupRouteItem)) {
            throw InvalidConfigException::create(static::CONFIG_NAME);
        }
    }
}