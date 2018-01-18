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
            ],

            'ecamp.api.auth'  => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/auth[/:action]',
                    'defaults' => [
                        'controller' => \eCamp\Api\Controller\AuthController::class,
                        'action' => 'index'
                    ],
                ],
            ],

            'ecamp.api.docu' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/docu',
                    'defaults' => [
                        'controller' => \eCamp\Api\Controller\SwaggerController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            eCamp\Api\Controller\IndexController::class => eCamp\Api\Controller\IndexControllerFactory::class,
            eCamp\Api\Controller\AuthController::class => eCamp\Api\Controller\AuthControllerFactory::class,
            eCamp\Api\Controller\SwaggerController::class => eCamp\Api\Controller\SwaggerControllerFactory::class,
        ]
    ],

    /*
    'zf-mvc-auth' => [
        'authentication' => [
            'adapters' => [
                'basic' => [
                    'adapter' => 'ZF\MvcAuth\Authentication\HttpAdapter',
                    'options' => [
                        'accept_schemes' => ['basic'],
                        'realm' => 'eCamp',
                        'nonce_timeout' => 3600
                    ],
                ],
            ]
        ]
    ]
    */
];
