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
            \eCamp\Core\Controller\Auth\GoogleController::class => \Zend\Mvc\Controller\LazyControllerAbstractFactory::class
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

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

];
