<?php
return [

    'router' => [
        'routes' => [
            'ecamp.web' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\IndexController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'ecamp.web.login' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\LoginController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'ecamp.web.user' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\UserController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'ecamp.web.group' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/group',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\GroupController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'ecamp.web.camp' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/camp',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\CampController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Web\Controller\IndexController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\LoginController::class => \eCamp\Web\ControllerFactory\LoginControllerFactory::class,

            \eCamp\Web\Controller\UserController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\GroupController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ],

    'view_manager' => [
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',

        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

];
