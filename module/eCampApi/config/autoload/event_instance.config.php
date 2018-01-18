<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_instance' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_instance[/:event_instance_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventInstanceApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventInstanceApiController::class => [
            'listener' => \eCamp\Core\Service\EventInstanceService::class,
            'controller_class' => \eCamp\Api\RestController\EventInstanceApiController::class,
            'route_name' => 'ecamp.api.event_instance',
            'route_identifier_name' => 'event_instance_id',
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
            'entity_class' => \eCamp\Core\Entity\EventInstance::class,
            'collection_class' => \eCamp\Api\Collection\EventInstanceCollection::class,
            'service_name' => 'EventInstance',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventInstance::class => [
                'route_identifier_name' => 'event_instance_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_instance',
                'hydrator' => eCamp\Core\Hydrator\EventInstanceHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventInstanceCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_instance',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
