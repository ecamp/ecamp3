<?php
return [

    'service_manager' => [
        'factories' => [
            \eCamp\Web\View\TwigExtensions::class => \ZendTwig\Service\TwigExtensionFactory::class,
        ],
    ],

    'route_manager' => [
        'factories' => [
            \eCamp\Web\Route\UserRouter::class => \eCamp\Web\Route\FluentRouterFactory::class,
            \eCamp\Web\Route\GroupRouter::class => \eCamp\Web\Route\FluentRouterFactory::class,
            \eCamp\Web\Route\CampRouter::class => \eCamp\Web\Route\FluentRouterFactory::class,
        ]
    ],

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


                    'user' => [
                        'type' => \eCamp\Web\Route\UserRouter::class,
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\UserController::class,
                                'action' => 'index'
                            ],
                        ]
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
                    'group' => [
                        'type' => \eCamp\Web\Route\GroupRouter::class,
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\GroupController::class,
                                'action' => 'index'
                            ],
                        ]
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
                    'camp' => [
                        'type' => \eCamp\Web\Route\CampRouter::class,
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\Camp\CampController::class,
                                'action' => 'index'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'collaborators' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/collaborators',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\CollaboratorsController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ]
                        ]
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
            \eCamp\Web\Controller\CampsController::class => \eCamp\Web\ControllerFactory\CampsControllerFactory::class,

            \eCamp\Web\Controller\UserController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\GroupController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Web\Controller\Camp\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\CollaboratorsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ],

    'translator' => [

    ],

    'view_manager' => [
        'doctype'               => 'HTML5',
        'not_found_template'    => 'e-camp/web-error/404',
        'exception_template'    => 'e-camp/web-error/index',

        'template_map' => [
            // obsolete:
            'layout/layout'     => __DIR__ . '/../view/e-camp/web-layout/layout.twig',
        ],

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'zend_twig'       => [
        'extensions' => [
            \eCamp\Web\View\TwigExtensions::class,
        ],
    ],

];
