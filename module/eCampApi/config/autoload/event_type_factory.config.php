<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_type_factory' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_type_factory[/:event_type_factory_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventTypeFactoryApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventTypeFactoryApiController::class => [
            'listener' => \eCamp\Core\Service\EventTypeFactoryService::class,
            'controller_class' => \eCamp\Api\RestController\EventTypeFactoryApiController::class,
            'route_name' => 'ecamp.api.event_type_factory',
            'route_identifier_name' => 'event_type_factory_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\EventTypeFactory::class,
            'collection_class' => \eCamp\Api\Collection\EventTypeFactoryCollection::class,
            'service_name' => 'EventTypeFactory',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventTypeFactory::class => [
                'route_identifier_name' => 'event_type_factory_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type_factory',
                'hydrator' => eCamp\Core\Hydrator\EventTypeFactoryHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventTypeFactoryCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type_factory',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
