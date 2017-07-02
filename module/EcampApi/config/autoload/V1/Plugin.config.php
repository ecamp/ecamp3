<?php

return [	
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/plugin[/:plugin_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\Plugin\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.plugin',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\Plugin\\Controller' => [
            'listener' => \EcampApi\V1\Rest\Plugin\PluginResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.plugin',
            'route_identifier_name' => 'plugin_id',
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
            'entity_class' => \EcampCore\Entity\Plugin::class,
            'collection_class' => \EcampApi\V1\Rest\Plugin\PluginCollection::class,
            'service_name' => 'Plugin',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\Plugin\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\Plugin\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\Plugin\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\Plugin::class => [
                'route_identifier_name' => 'plugin_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.plugin',
            	'hydrator' => EcampApi\V1\Rest\Plugin\PluginHydrator::class,
            ],
            \EcampApi\V1\Rest\Plugin\PluginCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.plugin',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\Plugin\PluginResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => EcampApi\V1\Rest\Plugin\PluginHydrator::class,
            ],
        ],
    ],
    'doctrine-hydrator' => [
    	EcampApi\V1\Rest\Plugin\PluginHydrator::class=> [
    		'object_manager' => 'doctrine.entitymanager.orm_default',
			'entity_class' => \EcampCore\Entity\Plugin::class,
      		'by_value' => true,
    		'filters' => [
    			[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
    		]
        ]
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\Plugin\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\Plugin\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\Plugin\\Validator' => [],
    ],
];
