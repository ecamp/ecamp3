<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.group_membership' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/group_membership[/:group_membership_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\GroupMembershipApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\GroupMembershipApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\GroupMembershipApiController::class => [
            'listener' => \eCamp\Core\EntityService\GroupMembershipService::class,
            'controller_class' => \eCamp\Api\RestController\GroupMembershipApiController::class,
            'route_name' => 'ecamp.api.group_membership',
            'route_identifier_name' => 'group_membership_id',
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
            'entity_class' => \eCamp\Core\Entity\GroupMembership::class,
            'collection_class' => \eCamp\Api\Collection\GroupMembershipCollection::class,
            'service_name' => 'GroupMembership',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\GroupMembership::class => [
                'route_identifier_name' => 'group_membership_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.group_membership',
                'hydrator' => eCamp\Core\Hydrator\GroupMembershipHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\GroupMembershipCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.group_membership',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
