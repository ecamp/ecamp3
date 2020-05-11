<?php

return [
    'doctrine' => [
        'driver' => [
            'ecamp_lib_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\Lib\Entity' => 'ecamp_lib_entities',
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'naming_strategy' => eCamp\Lib\Entity\CamelPascalNamingStrategy::class,
            ],
        ],
    ],

    'dependencies' => [
        'auto' => [
            'preferences' => [
                \Doctrine\ORM\EntityManager::class => 'doctrine.entitymanager.orm_default',
                \Interop\Container\ContainerInterface::class => Laminas\ServiceManager\ServiceManager::class,
            ],
            'types' => [
                \ZendTwig\Extension\Extension::class => [
                    'preferences' => [
                        \Interop\Container\ContainerInterface::class => Laminas\ServiceManager\ServiceManager::class,
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            \Laminas\Mail\Transport\TransportInterface::class => \eCamp\Lib\Mail\TransportFactory::class,
            \Laminas\ApiTools\Hal\Extractor\LinkExtractor::class => \eCamp\Lib\Hal\Factory\LinkExtractorFactory::class,
            \eCamp\Lib\Twig\TwigExtensions::class => \ZendTwig\Service\TwigExtensionFactory::class,
            \eCamp\Lib\ServiceManager\EntityFilterManager::class => \eCamp\Lib\ServiceManager\EntityFilterManagerFactory::class,
            Laminas\Hydrator\HydratorPluginManager::class => Laminas\Hydrator\HydratorPluginManagerFactory::class,
        ],
    ],
    'entity_filter' => [
        'abstract_factories' => [
            \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
        ],
    ],
    'zend_twig' => [
        'extensions' => [
            \eCamp\Lib\Twig\TwigExtensions::class,
        ],
    ],
];
