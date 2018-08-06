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
        'initializers' => [
            \eCamp\Core\Plugin\PluginStrategyProviderInjector::class,
            \eCamp\Core\ServiceManager\EntityServiceInjector::class,
            \eCamp\Core\ServiceManager\AuthUserProviderInjector::class,
        ],
        'aliases' => [
            \Zend\Permissions\Acl\AclInterface::class => \eCamp\Lib\Acl\Acl::class
        ],
        'factories' => [
            \eCamp\Lib\Acl\Acl::class => \eCamp\Core\Acl\AclFactory::class,

            \eCamp\Core\Auth\AuthUserProvider::class => \eCamp\Core\Auth\AuthUserProviderFactory::class,
            \eCamp\Core\Auth\AuthService::class => \eCamp\Core\Auth\AuthServiceFactory::class,


            \eCamp\Core\EntityService\MediumService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\OrganizationService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\GroupService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\GroupMembershipService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Core\EntityService\UserService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\UserIdentityService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Core\EntityService\PluginService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\CampTypeService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventTypeService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventTypePluginService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventTypeFactoryService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventTemplateService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventTemplateContainerService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Core\EntityService\CampService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\JobService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\JobRespService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventCategoryService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Core\EntityService\CampCollaborationService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\PeriodService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\DayService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,

            \eCamp\Core\EntityService\EventService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventPluginService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \eCamp\Core\EntityService\EventInstanceService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,


            \eCamp\Core\Plugin\PluginStrategyProvider::class =>\eCamp\Core\Plugin\PluginStrategyProviderFactory::class,


            \eCamp\Core\Service\RegisterService::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ]
    ],

    'entity_filter' => [
        'initializers' => [
            \eCamp\Core\ServiceManager\AuthUserProviderInjector::class,
        ],
    ],

    'controllers' => [
        'initializers' => [
            \eCamp\Core\ServiceManager\EntityServiceInjector::class,
        ],
        'factories' => [
            \eCamp\Core\Controller\Auth\GoogleController::class => \eCamp\Core\Controller\Auth\GoogleControllerFactory::class,
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
