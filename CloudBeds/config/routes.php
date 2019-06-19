<?php

use Core\Config\RouteConfigKeys;

return [
    RouteConfigKeys::GROUP_ROUTES => [
        [
            RouteConfigKeys::GROUP_BASE => '/api',
            RouteConfigKeys::GROUP_ITEMS => [
                [
                    RouteConfigKeys::ROUTE => '/interval/all',
                    RouteConfigKeys::HTTP_METHOD => 'GET',
                    RouteConfigKeys::CONTROLLER => App\Controllers\IntervalController::class,
                    RouteConfigKeys::CONTROLLER_ACTION => 'getAll'
                ],
                [
                    RouteConfigKeys::ROUTE => '/interval/one/{id:\d+}',
                    RouteConfigKeys::HTTP_METHOD => 'GET',
                    RouteConfigKeys::CONTROLLER => App\Controllers\IntervalController::class,
                    RouteConfigKeys::CONTROLLER_ACTION => 'getOne'
                ],
                [
                    RouteConfigKeys::ROUTE => '/interval/add',
                    RouteConfigKeys::HTTP_METHOD => 'POST',
                    RouteConfigKeys::CONTROLLER => App\Controllers\IntervalController::class,
                    RouteConfigKeys::CONTROLLER_ACTION => 'add'
                ],
                [
                    RouteConfigKeys::ROUTE => '/interval/deleteall',
                    RouteConfigKeys::HTTP_METHOD => 'GET',
                    RouteConfigKeys::CONTROLLER => App\Controllers\IntervalController::class,
                    RouteConfigKeys::CONTROLLER_ACTION => 'deleteAll'
                ],
            ]
        ],
        [
        RouteConfigKeys::GROUP_BASE => '/',
        RouteConfigKeys::GROUP_ITEMS => [
            [
                RouteConfigKeys::ROUTE => 'index',
                RouteConfigKeys::HTTP_METHOD => 'GET',
                RouteConfigKeys::CONTROLLER => App\Controllers\IntervalController::class,
                RouteConfigKeys::CONTROLLER_ACTION => 'index'
            ],
        ],
    ]
    ]
];
