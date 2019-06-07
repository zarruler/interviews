<?php

use Core\Config\RouteConfigKeys;

return [
    RouteConfigKeys::GROUP_ROUTES => [
        [
            RouteConfigKeys::GROUP_BASE => '/api',
            RouteConfigKeys::GROUP_ITEMS => [
                [
                    RouteConfigKeys::ROUTE => '/hello/world[/{id:\d+}]',
                    RouteConfigKeys::HTTP_METHOD => 'GET',
                    RouteConfigKeys::CONTROLLER => App\Controllers\Hello::class,
                    RouteConfigKeys::CONTROLLER_ACTION => 'world'
                ],
            ]
        ]
    ]
];
