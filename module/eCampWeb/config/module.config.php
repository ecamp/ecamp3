<?php
return [

    'route_manager' => [
        'factories' => [
            \eCamp\Web\Route\FluentRouter::class => \eCamp\Web\Route\FluentRouterFactory::class,
        ]
    ],

    'router' => [
        'routes' => [
            'ecamp.web' => [
                'type' => \eCamp\Web\Route\FluentRouter::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => \eCamp\Web\Controller\IndexController::class,
                        'action' => 'index'
                    ],

                    'user' => [
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\UserController::class,
                                'action' => 'index'
                            ],
                        ]
                    ],
                    'group' => [
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\GroupController::class,
                                'action' => 'index'
                            ],
                        ]
                    ],
                    'camp' => [
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\CampController::class,
                                'action' => 'index'
                            ],
                        ]
                    ],
                ],

                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'login[/:action]',
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\LoginController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],

                    'groups' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'groups',
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\GroupsController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],

                    'camps' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'camps',
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\CampsController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],
                ],
            ]
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Web\Controller\IndexController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\LoginController::class => \eCamp\Web\ControllerFactory\LoginControllerFactory::class,

            \eCamp\Web\Controller\GroupsController::class => \eCamp\Web\ControllerFactory\GroupsControllerFactory::class,
            \eCamp\Web\Controller\CampsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Web\Controller\UserController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\GroupController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ],

    'translator' => [

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
