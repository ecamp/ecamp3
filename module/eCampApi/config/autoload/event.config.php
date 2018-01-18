<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event[/:event_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventApiController::class => [
            'listener' => \eCamp\Core\Service\EventService::class,
            'controller_class' => \eCamp\Api\RestController\EventApiController::class,
            'route_name' => 'ecamp.api.event',
            'route_identifier_name' => 'event_id',
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
            'entity_class' => \eCamp\Core\Entity\Event::class,
            'collection_class' => \eCamp\Api\Collection\EventCollection::class,
            'service_name' => 'Event',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Event::class => [
                'route_identifier_name' => 'event_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event',
                'hydrator' => eCamp\Core\Hydrator\EventHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
