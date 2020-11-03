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
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'pbsmidata' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/pbsmidata[/:action]',
                            'defaults' => [
                                'controller' => \eCamp\Core\Controller\Auth\PbsMiDataController::class,
                                'action' => 'index',
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
            ],
        ],
    ],

    'service_manager' => [
        'aliases' => [
            \Laminas\Permissions\Acl\AclInterface::class => \eCamp\Lib\Acl\Acl::class,
        ],
        'factories' => [
            \eCamp\Lib\Acl\Acl::class => \eCamp\Core\Acl\AclFactory::class,

            \eCamp\Core\Auth\AuthUserProvider::class => \eCamp\Core\Auth\AuthUserProviderFactory::class,
            \eCamp\Core\Service\SendmailService::class => \eCamp\Core\Service\SendmailServiceFactory::class,
        ],

        // Use lazy services (service proxies) for expensive constructors or in case circular dependencies are needed
        'lazy_services' => [
            'class_map' => [
                \eCamp\Core\EntityService\ActivityCategoryService::class => \eCamp\Core\EntityService\ActivityCategoryService::class,
            ],
        ],
        'delegators' => [
            \eCamp\Core\EntityService\ActivityCategoryService::class => [
                Laminas\ServiceManager\Proxy\LazyServiceFactory::class,
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Core\Controller\Auth\GoogleController::class => \eCamp\Core\Controller\Auth\GoogleControllerFactory::class,
            \eCamp\Core\Controller\Auth\PbsMiDataController::class => \eCamp\Core\Controller\Auth\PbsMiDataControllerFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
            \eCamp\Core\Hydrator\ActivityContentHydrator::class => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
        ],
    ],

    'doctrine' => [
        'driver' => [
            'ecamp_core_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\Core\Entity' => 'ecamp_core_entities',
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'layout' => 'layout/layout',
        'template_map' => [
            'layout/layout' => __DIR__.'/../view/layout/layout.phtml',
            'error/404' => __DIR__.'/../view/error/404.phtml',
            'error/index' => __DIR__.'/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__.'/../view',
        ],
    ],

    'ecamp' => [
        'mail' => [
            'from' => 'info@ecamp3.ch',
        ],
        'laminas_mail' => [
            'register' => [
                'type' => \Laminas\Mime\Mime::MULTIPART_ALTERNATIVE,
                'parts' => [
                    [
                        'type' => \Laminas\Mime\Mime::TYPE_TEXT,
                        'template' => 'mail/register-text',
                        'encoding' => \Laminas\Mime\Mime::ENCODING_8BIT,
                        'charset' => 'utf-8',
                    ],
                    [
                        'type' => \Laminas\Mime\Mime::TYPE_HTML,
                        'template' => 'mail/register-html',
                        'encoding' => \Laminas\Mime\Mime::ENCODING_8BIT,
                        'charset' => 'utf-8',
                    ],
                ],
            ],
        ],
    ],
];
