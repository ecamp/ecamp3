<?php

return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.medium' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/medium[/:medium_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\Medium\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.medium',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\Medium\\Controller' => [
            'listener' => \EcampApi\V1\Rest\Medium\MediumResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.medium',
            'route_identifier_name' => 'medium_id',
            'entity_identifier_name' => 'name',
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
            'entity_class' => \EcampCore\Entity\Medium::class,
            'collection_class' => \EcampApi\V1\Rest\Medium\MediumCollection::class,
            'service_name' => 'Medium',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\Medium\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\Medium\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\Medium\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\Medium::class => [
                'route_identifier_name' => 'medium_id',
                'entity_identifier_name' => 'name',
                'route_name' => 'ecamp-api.rest.doctrine.medium',
                'hydrator' => 'EcampApi\\V1\\Rest\\Medium\\MediumHydrator',
            ],
            \EcampApi\V1\Rest\Medium\MediumCollection::class => [
                'entity_identifier_name' => 'name',
                'route_name' => 'ecamp-api.rest.doctrine.medium',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\Medium\MediumResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\Medium\\MediumHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\Medium\\MediumHydrator' => [
            'entity_class' => \EcampCore\Entity\Medium::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'by_value' => true,
        	'filters' => [
        		[ 'filter' => EcampApi\Hydrator\Filter\BaseEntityFilter::class ]
        	]
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\Medium\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\Medium\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\Medium\\Validator' => [],
    ],
];
