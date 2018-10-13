<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/plugin[/:plugin_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\PluginApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\PluginApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\PluginApiController::class => [
            'listener' => \eCamp\Core\EntityService\PluginService::class,
            'controller_class' => \eCamp\Api\RestController\PluginApiController::class,
            'route_name' => 'ecamp.api.plugin',
            'route_identifier_name' => 'plugin_id',
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
            'entity_class' => \eCamp\Core\Entity\Plugin::class,
            'collection_class' => \eCamp\Api\Collection\PluginCollection::class,
            'service_name' => 'Plugin',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Plugin::class => [
                'route_identifier_name' => 'plugin_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.plugin',
                'hydrator' => eCamp\Core\Hydrator\PluginHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\PluginCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.plugin',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
