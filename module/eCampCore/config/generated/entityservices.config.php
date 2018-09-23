<?php
return [

    'lazy_services' => [
        'class_map' => [
            \eCamp\Core\EntityService\EventCategoryService::class => \eCamp\Core\EntityService\EventCategoryService::class,
        ],
    ],
    'delegators' => [
        \eCamp\Core\EntityService\EventCategoryService::class => [
            Zend\ServiceManager\Proxy\LazyServiceFactory::class,
        ],
    ],

];