<?php

namespace eCamp\AoT;

use Laminas\Di\InjectorInterface;

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
            InjectorInterface::class => [
                InjectorDecoratorFactory::class,
            ],
        ],
    ],

    'entity_filter' => [
        'factories' => file_exists(__DIR__.'/../gen/factories.php') ? include __DIR__.'/../gen/factories.php' : [],
    ],
];
