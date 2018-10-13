<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.group' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/group[/:group_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\GroupApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\GroupApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\GroupApiController::class => [
            'listener' => \eCamp\Core\EntityService\GroupService::class,
            'controller_class' => \eCamp\Api\RestController\GroupApiController::class,
            'route_name' => 'ecamp.api.group',
            'route_identifier_name' => 'group_id',
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
            'entity_class' => \eCamp\Core\Entity\Group::class,
            'collection_class' => \eCamp\Api\Collection\GroupCollection::class,
            'service_name' => 'Group',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Group::class => [
                'route_identifier_name' => 'group_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.group',
                'hydrator' => eCamp\Core\Hydrator\GroupHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\GroupCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.group',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
