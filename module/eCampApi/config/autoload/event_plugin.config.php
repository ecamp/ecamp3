<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_plugin[/:event_plugin_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventPluginApiController::class,
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventPluginApiController::class => [
            'listener' => \eCamp\Core\Service\EventPluginService::class,
            'controller_class' => \eCamp\Api\RestController\EventPluginApiController::class,
            'route_name' => 'ecamp.api.event_plugin',
            'route_identifier_name' => 'event_plugin_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                2 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [ 'event_id' ],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\EventPlugin::class,
            'collection_class' => \eCamp\Api\Collection\EventPluginCollection::class,
            'service_name' => 'EventPlugin',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventPlugin::class => [
                'route_identifier_name' => 'event_plugin_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_plugin',
                'hydrator' => eCamp\Core\Hydrator\EventPluginHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventPluginCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_plugin',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
