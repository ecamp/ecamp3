<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_category' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_category[/:event_category_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventCategoryApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\EventCategoryApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventCategoryApiController::class => [
            'listener' => \eCamp\Core\EntityService\EventCategoryService::class,
            'controller_class' => \eCamp\Api\RestController\EventCategoryApiController::class,
            'route_name' => 'ecamp.api.event_category',
            'route_identifier_name' => 'event_category_id',
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
            'entity_class' => \eCamp\Core\Entity\EventCategory::class,
            'collection_class' => \eCamp\Api\Collection\EventCategoryCollection::class,
            'service_name' => 'EventCategory',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventCategory::class => [
                'route_identifier_name' => 'event_category_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_category',
                'hydrator' => eCamp\Core\Hydrator\EventCategoryHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventCategoryCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_category',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
