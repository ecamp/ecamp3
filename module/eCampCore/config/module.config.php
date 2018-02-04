<?php

return [

    'router' => [
        'routes' => [
            'ecamp.auth.google' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/auth/google[/:action]',
                    'defaults' => [
                        'controller' => \eCamp\Core\Controller\Auth\GoogleController::class,
                        'action' => 'index'
                    ],
                ],
            ]
        ]
    ],

    'service_manager' => [
        'aliases' => [
            \Zend\Permissions\Acl\AclInterface::class => \eCamp\Lib\Acl\Acl::class
        ],
        'factories' => [
            \eCamp\Lib\Acl\Acl::class => \eCamp\Core\Acl\AclFactory::class,

            \eCamp\Core\Auth\AuthService::class => \eCamp\Core\Auth\AuthServiceFactory::class,

            \eCamp\Core\Plugin\PluginStrategyProvider::class =>\eCamp\Core\Plugin\PluginStrategyProviderFactory::class,


            \eCamp\Core\Service\MediumService::class => \eCamp\Core\ServiceFactory\MediumServiceFactory::class,
            \eCamp\Core\Service\OrganizationService::class => \eCamp\Core\ServiceFactory\OrganizationServiceFactory::class,
            \eCamp\Core\Service\GroupService::class => \eCamp\Core\ServiceFactory\GroupServiceFactory::class,
            \eCamp\Core\Service\GroupMembershipService::class => \eCamp\Core\ServiceFactory\GroupMembershipServiceFactory::class,

            \eCamp\Core\Service\UserService::class => \eCamp\Core\ServiceFactory\UserServiceFactory::class,
            \eCamp\Core\Service\UserIdentityService::class => \eCamp\Core\ServiceFactory\UserIdentityServiceFactory::class,

            \eCamp\Core\Service\PluginService::class => \eCamp\Core\ServiceFactory\PluginServiceFactory::class,
            \eCamp\Core\Service\CampTypeService::class => \eCamp\Core\ServiceFactory\CampTypeServiceFactory::class,
            \eCamp\Core\Service\EventTypeService::class => \eCamp\Core\ServiceFactory\EventTypeServiceFactory::class,
            \eCamp\Core\Service\EventTypePluginService::class => \eCamp\Core\ServiceFactory\EventTypePluginServiceFactory::class,
            \eCamp\Core\Service\EventTypeFactoryService::class => \eCamp\Core\ServiceFactory\EventTypeFactoryServiceFactory::class,
            \eCamp\Core\Service\EventTemplateService::class => \eCamp\Core\ServiceFactory\EventTemplateServiceFactory::class,
            \eCamp\Core\Service\EventTemplateContainerService::class => \eCamp\Core\ServiceFactory\EventTemplateContainerServiceFactory::class,

            \eCamp\Core\Service\CampService::class => \eCamp\Core\ServiceFactory\CampServiceFactory::class,
            \eCamp\Core\Service\JobService::class => \eCamp\Core\ServiceFactory\JobServiceFactory::class,
            \eCamp\Core\Service\JobRespService::class => \eCamp\Core\ServiceFactory\JobRespServiceFactory::class,
            \eCamp\Core\Service\EventCategoryService::class => \eCamp\Core\ServiceFactory\EventCategoryServiceFactory::class,

            \eCamp\Core\Service\CampCollaborationService::class => \eCamp\Core\ServiceFactory\CampCollaborationServiceFactory::class,
            \eCamp\Core\Service\PeriodService::class => \eCamp\Core\ServiceFactory\PeriodServiceFactory::class,
            \eCamp\Core\Service\DayService::class => \eCamp\Core\ServiceFactory\DayServiceFactory::class,

            \eCamp\Core\Service\EventService::class => \eCamp\Core\ServiceFactory\EventServiceFactory::class,
            \eCamp\Core\Service\EventPluginService::class => \eCamp\Core\ServiceFactory\EventPluginServiceFactory::class,
            \eCamp\Core\Service\EventInstanceService::class => \eCamp\Core\ServiceFactory\EventInstanceServiceFactory::class,
        ]
    ],

    'controllers' => [
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
