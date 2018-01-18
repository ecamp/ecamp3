<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.camp_collaboration' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp_collaboration[/:camp_collaboration_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\CampCollaborationApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\CampCollaborationApiController::class => [
            'listener' => \eCamp\Core\Service\CampCollaborationService::class,
            'controller_class' => \eCamp\Api\RestController\CampCollaborationApiController::class,
            'route_name' => 'ecamp.api.camp_collaboration',
            'route_identifier_name' => 'camp_collaboration_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PUT',
                2 => 'DELETE'
            ],
            'collection_http_methods' => [
                0 => 'GET',
                2 => 'POST'
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\CampCollaboration::class,
            'collection_class' => \eCamp\Api\Collection\CampCollaborationCollection::class,
            'service_name' => 'CampCollaboration',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\CampCollaboration::class => [
                'route_identifier_name' => 'camp_collaboration_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp_collaboration',
                'hydrator' => eCamp\Core\Hydrator\CampCollaborationHydrator::class,
                'max_depth' => 2,
            ],
            \eCamp\Api\Collection\CampCollaborationCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.camp_collaboration',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
