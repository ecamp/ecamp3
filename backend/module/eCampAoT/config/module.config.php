<?php

namespace eCamp\AoT;

return [
    'dependencies' => [
        'auto' => [
            'aot' => [
                'namespace' => __NAMESPACE__.'\\Generated',
                'directory' => __DIR__.'/../gen',
            ],
        ],
    ],

    'controllers' => [
        'factories' => file_exists(__DIR__.'/../gen/factories.php') ? include __DIR__.'/../gen/factories.php' : [],
    ],

    'service_manager' => [
        'factories' => file_exists(__DIR__.'/../gen/factories.php') ? include __DIR__.'/../gen/factories.php' : [],
        'delegators' => [
            \Laminas\Di\InjectorInterface::class => [
                InjectorDecoratorFactory::class,
            ],
        ],
    ],

    'entity_filter' => [
        'factories' => file_exists(__DIR__.'/../gen/factories.php') ? include __DIR__.'/../gen/factories.php' : [],
    ],
];
