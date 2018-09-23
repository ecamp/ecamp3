<?php

return [

    'dependencies' => [
        'auto' => [
            'preferences' => [
                // A map of classname => preferred type
                \Interop\Container\ContainerInterface::class => Zend\ServiceManager\ServiceManager::class,
                \Doctrine\ORM\EntityManager::class => 'doctrine.entitymanager.orm_default'
            ]
        ],

    ],
    
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

    'service_manager' => \Zend\Stdlib\ArrayUtils::merge(
        file_exists(__DIR__ . '/generated/entityservices.config.php') ? include __DIR__ . '/generated/entityservices.config.php' : []
        ,
        [
            'aliases' => [
                \Zend\Permissions\Acl\AclInterface::class => \eCamp\Lib\Acl\Acl::class,
            ],
            'factories' => [
                \eCamp\Lib\Acl\Acl::class => \eCamp\Core\Acl\AclFactory::class,

                \eCamp\Core\Auth\AuthUserProvider::class => \eCamp\Core\Auth\AuthUserProviderFactory::class,
                \eCamp\Core\Auth\AuthService::class => \eCamp\Core\Auth\AuthServiceFactory::class,

                \eCamp\Core\Plugin\PluginStrategyProvider::class =>\eCamp\Core\Plugin\PluginStrategyProviderFactory::class,
            ]
        ]
    ),

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
