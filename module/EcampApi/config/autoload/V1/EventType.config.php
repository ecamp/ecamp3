<?php
return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.event-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-type[/:event_type_id]',
                    'defaults' => [
                        'controller' => EcampApi\V1\Rest\EventType\Controller::class,
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.event-type',
        ],
    ],
    'zf-rest' => [
		EcampApi\V1\Rest\EventType\Controller::class => [
            'listener' => \EcampApi\V1\Rest\EventType\EventTypeResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.event-type',
            'route_identifier_name' => 'event_type_id',
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
            'entity_class' => \EcampCore\Entity\EventType::class,
            'collection_class' => \EcampApi\V1\Rest\EventType\EventTypeCollection::class,
            'service_name' => 'EventType',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
			EcampApi\V1\Rest\EventType\Controller::class => 'HalJson',
        ],
        'accept-whitelist' => [
       		EcampApi\V1\Rest\EventType\Controller::class=> [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
        	EcampApi\V1\Rest\EventType\Controller::class => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\EventType::class => [
                'route_identifier_name' => 'event_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-type',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventType\\EventTypeHydrator',
            ],
            \EcampApi\V1\Rest\EventType\EventTypeCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.event-type',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\EventType\EventTypeResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\EventType\\EventTypeHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\EventType\\EventTypeHydrator' => [
            'entity_class' => \EcampCore\Entity\EventType::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'by_value' => true,
        	'strategies' => [
        		'campTypes' => ZF\Doctrine\Hydrator\Strategy\CollectionExtract::class
        	],
        	'filters' => [
        		[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
        	]
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\EventType\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\EventType\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\EventType\\Validator' => [],
    ],
];
