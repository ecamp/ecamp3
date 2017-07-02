<?php
return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.event-template-container' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-template-container[/:event_template_container_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.event-template-container',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller' => [
            'listener' => \EcampApi\V1\Rest\EventTemplateContainer\EventTemplateContainerResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.event-template-container',
            'route_identifier_name' => 'event_template_container_id',
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
            'entity_class' => \EcampCore\Entity\EventTemplateContainer::class,
            'collection_class' => \EcampApi\V1\Rest\EventTemplateContainer\EventTemplateContainerCollection::class,
            'service_name' => 'EventTemplateContainer',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\EventTemplateContainer::class => [
                'route_identifier_name' => 'event_template_container_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-template-container',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTemplateContainer\\EventTemplateContainerHydrator',
            ],
            \EcampApi\V1\Rest\EventTemplateContainer\EventTemplateContainerCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-template-container',
                'is_collection' => true,
            	'max_depth' => 1,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\EventTemplateContainer\EventTemplateContainerResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventTemplateContainer\\EventTemplateContainerHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\EventTemplateContainer\\EventTemplateContainerHydrator' => [
            'entity_class' => \EcampCore\Entity\EventTemplateContainer::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'by_value' => true,
        	'filters' => [
        		[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
        	]
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\EventTemplateContainer\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\EventTemplateContainer\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\EventTemplateContainer\\Validator' => [],
    ],
];
