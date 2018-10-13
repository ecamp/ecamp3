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

                    'register' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => 'register[/:action]',
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\RegisterController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],


                    'user' => [
                        'type' => \eCamp\Web\Route\UserRouter::class,
                        'options' => [
                            'defaults' => [
                                'controller' => \eCamp\Web\Controller\User\UserController::class,
                                'action' => 'index'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'membership' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/membership',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\User\MembershipController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'camps' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/camps',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\User\CampController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'friends' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/friends',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\User\FriendsController::class,
                                        'action' => 'index'
                                    ],
                                ],
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
                                'controller' => \eCamp\Web\Controller\Group\GroupController::class,
                                'action' => 'index'
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'membership' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/membership',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Group\MembershipController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'camps' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/camps',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Group\CampController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'admin' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/admin',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Group\AdminController::class,
                                        'action' => 'index'
                                    ],
                                ],
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
                            'periods' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/periods',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\PeriodsController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'picasso' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/picasso',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\PicassoController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'tasks' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/tasks',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\TasksController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'collaborators' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/collaborators',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\CollaboratorsController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'print' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/print',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\PrintController::class,
                                        'action' => 'index'
                                    ],
                                ],
                            ],
                            'settings' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/settings',
                                    'defaults' => [
                                        'controller' => \eCamp\Web\Controller\Camp\SettingsController::class,
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
            \eCamp\Web\Controller\LoginController::class => \Zend\Mvc\Controller\LazyControllerAbstractFactory::class,
            \eCamp\Web\Controller\RegisterController::class => \Zend\Mvc\Controller\LazyControllerAbstractFactory::class,

            \eCamp\Web\Controller\GroupsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            /**
             * use LazyControllerAbstractFactory for constructor based injection of controller dependencies
             */
            \eCamp\Web\Controller\CampsController::class => \Zend\Mvc\Controller\LazyControllerAbstractFactory::class,

            \eCamp\Web\Controller\User\UserController::class =>  \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\User\MembershipController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\User\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\User\FriendsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Web\Controller\Group\GroupController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Group\MembershipController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Group\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Group\AdminController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Web\Controller\Camp\CampController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\PeriodsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\PicassoController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\TasksController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\CollaboratorsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\PrintController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\Controller\Camp\SettingsController::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
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

    'asset_manager' => [
        'resolver_configs' => [
            'aliases' => [
                'assets/module/web/' => __DIR__ . '/../assets/',
            ],
        ],
    ],

    'zend_twig'       => [
        'extensions' => [
            \eCamp\Web\View\TwigExtensions::class,
        ],
    ],

    'view_helpers' => [
        'factories' => [
            \eCamp\Web\View\Helper\IncludeScriptIfPresent::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Web\View\Helper\IncludeStyleIfPresent::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Zend\View\Helper\Asset::class => \eCamp\Web\View\Helper\Service\RobustAssetFactory::class,
        ],
        'aliases' => [
            'includeScriptIfPresent' => \eCamp\Web\View\Helper\IncludeScriptIfPresent::class,
            'includeStyleIfPresent' => \eCamp\Web\View\Helper\IncludeStyleIfPresent::class,
        ]
    ],

    'view_helper_config' => [
        'asset' => [
            'resource_map' => json_decode(@file_get_contents(__DIR__ . '/../assets/assets.json') ?: '{}', true),
        ]
    ],

];
