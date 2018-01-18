<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.day' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/day[/:day_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\DayApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\DayApiController::class => [
            'listener' => \eCamp\Core\Service\DayService::class,
            'controller_class' => \eCamp\Api\RestController\DayApiController::class,
            'route_name' => 'ecamp.api.day',
            'route_identifier_name' => 'day_id',
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
            'entity_class' => \eCamp\Core\Entity\Day::class,
            'collection_class' => \eCamp\Api\Collection\DayCollection::class,
            'service_name' => 'Period',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Day::class => [
                'route_identifier_name' => 'day_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.day',
                'hydrator' => eCamp\Core\Hydrator\DayHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\DayCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.day',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
