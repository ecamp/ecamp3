<?php
return [
  
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.day' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/day[/:day_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\Day\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.day',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\Day\\Controller' => [
            'listener' => \EcampApi\V1\Rest\Day\DayResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.day',
            'route_identifier_name' => 'day_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \EcampCore\Entity\Day::class,
            'collection_class' => \EcampApi\V1\Rest\Day\DayCollection::class,
            'service_name' => 'Day',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\Day\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\Day\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\Day\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\Day::class => [
                'route_identifier_name' => 'day_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.day',
                'hydrator' => 'EcampApi\\V1\\Rest\\Day\\DayHydrator',
            ],
            \EcampApi\V1\Rest\Day\DayCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.day',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\Day\DayResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\Day\\DayHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\Day\\DayHydrator' => [
            'entity_class' => \EcampCore\Entity\Day::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\Day\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\Day\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\Day\\Validator' => [
            0 => [
                'name' => 'createdAt',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            1 => [
                'name' => 'updatedAt',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'dayOffset',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\Digits::class,
                    ],
                ],
                'validators' => [],
            ],
        ],
    ],
];
