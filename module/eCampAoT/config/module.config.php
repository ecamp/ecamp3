<?php

namespace eCamp\AoT;

use Zend\Di\InjectorInterface;

return [
    'service_manager' => [
        'factories' => 
            file_exists(__DIR__ . '/../gen/factories.php')
                ? include __DIR__ . '/../gen/factories.php' : []
        ,
        'delegators' => [
            InjectorInterface::class => [
                InjectorDecoratorFactory::class,
            ],
        ],
    ]
];