<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.medium' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/medium[/:medium_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\MediumApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\MediumApiController::class => [
            'listener' => \eCamp\Core\Service\MediumService::class,
            'controller_class' => \eCamp\Api\RestController\MediumApiController::class,
            'route_name' => 'ecamp.api.medium',
            'route_identifier_name' => 'medium_id',
            'entity_identifier_name' => 'name',
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
            'entity_class' => \eCamp\Core\Entity\Medium::class,
            'collection_class' => \eCamp\Api\Collection\MediumCollection::class,
            'service_name' => 'Medium',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Medium::class => [
                'route_identifier_name' => 'medium_id',
                'entity_identifier_name' => 'name',
                'route_name' => 'ecamp.api.medium',
                'hydrator' => eCamp\Core\Hydrator\MediumHydrator::class
            ],
            \eCamp\Api\Collection\MediumCollection::class => [
                'entity_identifier_name' => 'name',
                'route_name' => 'ecamp.api.medium',
                'is_collection' => true,
            ],
        ],
    ],

];
