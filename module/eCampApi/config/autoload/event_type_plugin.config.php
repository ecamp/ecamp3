<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_type_plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_type_plugin[/:event_type_plugin_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventTypePluginApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventTypePluginApiController::class => [
            'listener' => \eCamp\Core\EntityService\EventTypePluginService::class,
            'controller_class' => \eCamp\Api\RestController\EventTypePluginApiController::class,
            'route_name' => 'ecamp.api.event_type_plugin',
            'route_identifier_name' => 'event_type_plugin_id',
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
            'entity_class' => \eCamp\Core\Entity\EventTypePlugin::class,
            'collection_class' => \eCamp\Api\Collection\EventTypePluginCollection::class,
            'service_name' => 'EventTypePlugin',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventTypePlugin::class => [
                'route_identifier_name' => 'event_type_plugin_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type_plugin',
                'hydrator' => eCamp\Core\Hydrator\EventTypePluginHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventTypePluginCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_type_plugin',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
