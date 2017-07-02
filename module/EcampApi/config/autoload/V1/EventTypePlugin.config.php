<?php
return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.event-type-plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-type-plugin[/:event_type_plugin_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.event-type-plugin',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
            'listener' => \EcampApi\V1\Rest\EventTypePlugin\EventTypePluginResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.event-type-plugin',
            'route_identifier_name' => 'event_type_plugin_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \EcampCore\Entity\EventTypePlugin::class,
            'collection_class' => \EcampApi\V1\Rest\EventTypePlugin\EventTypePluginCollection::class,
            'service_name' => 'EventTypePlugin',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\EventTypePlugin::class => [
                'route_identifier_name' => 'event_type_plugin_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-type-plugin',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTypePlugin\\EventTypePluginHydrator',
            ],
            \EcampApi\V1\Rest\EventTypePlugin\EventTypePluginCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-type-plugin',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\EventTypePlugin\EventTypePluginResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTypePlugin\\EventTypePluginHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\EventTypePlugin\\EventTypePluginHydrator' => [
            'entity_class' => \EcampCore\Entity\EventTypePlugin::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'by_value' => true,
        	'filters' => [
        		[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
        	]
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\EventTypePlugin\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\EventTypePlugin\\Validator' => [],
    ],
];
