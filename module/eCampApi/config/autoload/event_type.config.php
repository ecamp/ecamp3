<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_type[/:event_type_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventTypeApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventTypeApiController::class => [
            'listener' => \eCamp\Core\Service\EventTypeService::class,
            'controller_class' => \eCamp\Api\RestController\EventTypeApiController::class,
            'route_name' => 'ecamp.api.event_type',
            'route_identifier_name' => 'event_type_id',
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
            'entity_class' => \eCamp\Core\Entity\EventType::class,
            'collection_class' => \eCamp\Api\Collection\EventTypeCollection::class,
            'service_name' => 'EventType',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventType::class => [
                'route_identifier_name' => 'event_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type',
                'hydrator' => eCamp\Core\Hydrator\EventTypeHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventTypeCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
