<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.camp' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp[/:camp_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\CampApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\CampApiController::class => [
            'listener' => \eCamp\Core\EntityService\CampService::class,
            'controller_class' => \eCamp\Api\RestController\CampApiController::class,
            'route_name' => 'ecamp.api.camp',
            'route_identifier_name' => 'camp_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST'
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\Camp::class,
            'collection_class' => \eCamp\Api\Collection\CampCollection::class,
            'service_name' => 'Camp',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Camp::class => [
                'route_identifier_name' => 'camp_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp',
                'hydrator' => eCamp\Core\Hydrator\CampHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\CampCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

    /*
    'zf-mvc-auth' => [
        'authorization' => [
            \eCamp\Api\RestController\CampApiController::class => [
                'collection' => [
                    'default' => true,
                    'GET' => false,
                    //'POST' => false,
                    // etc.
                ],
                'entity' => [
                    'default' => true,
                    'GET' => false,
                    //'POST' => boolean,
                    // etc.
                ],
            ]
        ]
    ]
    */
];
