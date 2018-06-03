<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.user' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/user[/:user_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\UserApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\UserApiController::class => [
            'listener' => \eCamp\Core\EntityService\UserService::class,
            'controller_class' => \eCamp\Api\RestController\UserApiController::class,
            'route_name' => 'ecamp.api.user',
            'route_identifier_name' => 'user_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\User::class,
            'collection_class' => \eCamp\Api\Collection\UserCollection::class,
            'service_name' => 'User',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\User::class => [
                'route_identifier_name' => 'user_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.user',
                'hydrator' => eCamp\Core\Hydrator\UserHydrator::class
            ],
            \eCamp\Api\Collection\UserCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.user',
                'is_collection' => true,
            ],
        ],
    ],

];
