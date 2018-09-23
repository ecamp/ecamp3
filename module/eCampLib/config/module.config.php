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
        'factories' => [
            \Zend\Mail\Transport\TransportInterface::class => \eCamp\Lib\Mail\TransportFactory::class,
            \eCamp\Lib\Twig\TwigExtensions::class => \ZendTwig\Service\TwigExtensionFactory::class,
            \eCamp\Lib\ServiceManager\EntityFilterManager::class => \eCamp\Lib\ServiceManager\EntityFilterManagerFactory::class,
        ],
    ],

    'zend_twig' => [
        'extensions' => [
            \eCamp\Lib\Twig\TwigExtensions::class,
        ],
    ],

];
