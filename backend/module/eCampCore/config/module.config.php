<?php

return [

    'router' => [
        'routes' => [
            'ecamp.auth' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/auth',
                ],

                'child_routes' => [
                    'google' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/google[/:action]',
                            'defaults' => [
                                'controller' => \eCamp\Core\Controller\Auth\GoogleController::class,
                                'action' => 'index'
                            ],
                        ],
                    ],

                    'facebook' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/facebook[/:action]',
//                            'defaults' => [
//                                'controller' => \eCamp\Core\Controller\Auth\FacebookController::class,
//                                'action' => 'index'
//                            ],
                        ],
                    ],
                ],
            ]
        ]
    ],

    'service_manager' => [
        'aliases' => [
            \Zend\Permissions\Acl\AclInterface::class => \eCamp\Lib\Acl\Acl::class,
        ],
        'factories' => [
            \eCamp\Lib\Acl\Acl::class => \eCamp\Core\Acl\AclFactory::class,

            \eCamp\Core\Auth\AuthUserProvider::class => \eCamp\Core\Auth\AuthUserProviderFactory::class,

            \eCamp\Core\Plugin\PluginStrategyProvider::class =>\eCamp\Core\Plugin\PluginStrategyProviderFactory::class,
        ],

        /**
         * Use lazy services (service proxies) for expensive constructors or in case circular dependencies are needed
         */
        'lazy_services' => [
            'class_map' => [
                \eCamp\Core\EntityService\EventCategoryService::class => \eCamp\Core\EntityService\EventCategoryService::class,
            ],
        ],
        'delegators' => [
            \eCamp\Core\EntityService\EventCategoryService::class => [
                Zend\ServiceManager\Proxy\LazyServiceFactory::class,
            ],
        ]
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Core\Controller\Auth\GoogleController::class => \eCamp\Core\Controller\Auth\GoogleControllerFactory::class
        ]
    ],

    'hydrators' => [
        'factories' => [
            \eCamp\Core\Hydrator\EventPluginHydrator::class => \eCamp\Core\HydratorFactory\EventPluginHydratorFactory::class
        ]
    ],

    'doctrine' => [
        'driver' => [
            'ecamp_core_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\Core\Entity' => 'ecamp_core_entities'
                ]
            ],
        ],
    ],

    'view_manager' => [

        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
        //'layout'                   => 'layout/layout',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
			'error/404'               => __DIR__ . '/../view/error/404.phtml',
			'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),

    ],

];
