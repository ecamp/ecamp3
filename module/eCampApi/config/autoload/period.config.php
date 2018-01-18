<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.period' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/period[/:period_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\PeriodApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\PeriodApiController::class => [
            'listener' => \eCamp\Core\Service\PeriodService::class,
            'controller_class' => \eCamp\Api\RestController\PeriodApiController::class,
            'route_name' => 'ecamp.api.period',
            'route_identifier_name' => 'period_id',
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
            'entity_class' => \eCamp\Core\Entity\Period::class,
            'collection_class' => \eCamp\Api\Collection\PeriodCollection::class,
            'service_name' => 'Period',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Period::class => [
                'route_identifier_name' => 'period_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.period',
                'hydrator' => eCamp\Core\Hydrator\PeriodHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\PeriodCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.period',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
