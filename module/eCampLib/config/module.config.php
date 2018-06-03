<?php
return [
    'doctrine' => [
        'driver' => [
            'ecamp_lib_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [ __DIR__ . '/../src/Entity' ]
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\Lib\Entity' => 'ecamp_lib_entities'
                ]
            ]
        ]
    ],

    'service_manager' => [
        'initializers' => [
            \eCamp\Lib\ServiceManager\AclInjector::class,
            \eCamp\Lib\ServiceManager\EntityManagerInjector::class,
            \eCamp\Lib\ServiceManager\EntityFilterManagerInjector::class,
            \eCamp\Lib\ServiceManager\HydratorPluginManagerInjector::class
        ],

        'factories' => [
            \Zend\Mail\Transport\TransportInterface::class => \eCamp\Lib\Mail\TransportFactory::class,

            \eCamp\Lib\Twig\TwigExtensions::class => \ZendTwig\Service\TwigExtensionFactory::class,

            \eCamp\Lib\ServiceManager\EntityFilterManager::class => \eCamp\Lib\ServiceManager\EntityFilterManagerFactory::class,
        ],
    ],

    'entity_filter' => [
        'initializers' => [
            \eCamp\Lib\ServiceManager\EntityManagerInjector::class,
            \eCamp\Lib\ServiceManager\EntityFilterManagerInjector::class
        ],
    ],

    'zend_twig' => [
        'extensions' => [
            \eCamp\Lib\Twig\TwigExtensions::class,
        ],
    ],

];
