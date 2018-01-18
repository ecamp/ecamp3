<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.camp_type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp_type[/:camp_type_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\CampTypeApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\CampTypeApiController::class => [
            'listener' => \eCamp\Core\Service\CampTypeService::class,
            'controller_class' => \eCamp\Api\RestController\CampTypeApiController::class,
            'route_name' => 'ecamp.api.camp_type',
            'route_identifier_name' => 'camp_type_id',
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
            'entity_class' => \eCamp\Core\Entity\CampType::class,
            'collection_class' => \eCamp\Api\Collection\CampTypeCollection::class,
            'service_name' => 'CampType',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\CampType::class => [
                'route_identifier_name' => 'camp_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp_type',
                'hydrator' => eCamp\Core\Hydrator\CampTypeHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\CampTypeCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp_type',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

    /*
    'zf-mvc-auth' => [
        'authorization' => [
            \eCamp\Api\RestController\CampTypeApiController::class => [
                'collection' => [
                    'default' => true,
                    //'GET' => false,
                    //'POST' => false,
                    // etc.
                ],
                'entity' => [
                    'default' => true,
                    //'GET' => false,
                    //'POST' => boolean,
                    // etc.
                ],
            ]
        ]
    ]
    */
];
