<?php

return [
    'router' => [
        'routes' => [

            'ecamp.api'  => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => \eCamp\Api\Controller\IndexController::class,
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/login[/:action]',
                            'defaults' => [
                                'controller' => \eCamp\Api\Controller\LoginController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => \eCamp\Api\Controller\LoginController::class,
                                'action' => 'logout'
                            ],
                        ],
                    ]
                ],
            ],

        ],
    ],

];
