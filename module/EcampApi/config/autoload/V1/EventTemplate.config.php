<?php
use Zend\Hydrator\Strategy\StrategyInterface;

return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.event-template' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-template[/:event_template_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\EventTemplate\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.event-template',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\EventTemplate\\Controller' => [
            'listener' => \EcampApi\V1\Rest\EventTemplate\EventTemplateResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.event-template',
            'route_identifier_name' => 'event_template_id',
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
            'entity_class' => \EcampCore\Entity\EventTemplate::class,
            'collection_class' => \EcampApi\V1\Rest\EventTemplate\EventTemplateCollection::class,
            'service_name' => 'EventTemplate',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\EventTemplate\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTemplate\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTemplate\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\EventTemplate::class => [
                'route_identifier_name' => 'event_template_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-template',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTemplate\\EventTemplateHydrator',
            	'max_depth' => 1
            ],
            \EcampApi\V1\Rest\EventTemplate\EventTemplateCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-template',
                'is_collection' => true,
            	'max_depth' => 0
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\EventTemplate\EventTemplateResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTemplate\\EventTemplateHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\EventTemplate\\EventTemplateHydrator' => [
            'entity_class' => \EcampCore\Entity\EventTemplate::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'by_value' => true,
        	'filters' => [
       			[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
       		]
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\EventTemplate\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\EventTemplate\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\EventTemplate\\Validator' => [],
    ],
];
