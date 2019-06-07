<?php

namespace Core;

use Core\Route\Router;
use Core\Exceptions\ApiException;

class App
{
    public function __construct(Router $router, Header $header)
    {

        try {
            $router->dispatchRoute();
        } catch (ApiException $e) {
            $header->sendCode($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            $header->sendCode($e->getMessage(), 500);
        }
    }

}