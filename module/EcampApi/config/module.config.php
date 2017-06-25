<?php
return [
    'router' => [
        'routes' => [
            'ecamp-api.rest.doctrine.camp-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp-type[/:camp_type_id]',
                    'defaults' => [
                        'controller' => 'EcampApi\\V1\\Rest\\CampType\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'ecamp-api.rest.doctrine.camp-type',
        ],
    ],
    'zf-rest' => [
        'EcampApi\\V1\\Rest\\CampType\\Controller' => [
            'listener' => \EcampApi\V1\Rest\CampType\CampTypeResource::class,
            'route_name' => 'ecamp-api.rest.doctrine.camp-type',
            'route_identifier_name' => 'camp_type_id',
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
            'entity_class' => \EcampCore\Entity\CampType::class,
            'collection_class' => \EcampApi\V1\Rest\CampType\CampTypeCollection::class,
            'service_name' => 'CampType',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'EcampApi\\V1\\Rest\\CampType\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'EcampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/vnd.ecamp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'EcampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \EcampCore\Entity\CampType::class => [
                'route_identifier_name' => 'camp_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.camp-type',
                'hydrator' => 'EcampApi\\V1\\Rest\\CampType\\CampTypeHydrator',
            ],
            \EcampApi\V1\Rest\CampType\CampTypeCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.camp-type',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\CampType\CampTypeResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'EcampApi\\V1\\Rest\\CampType\\CampTypeHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'EcampApi\\V1\\Rest\\CampType\\CampTypeHydrator' => [
            'entity_class' => \EcampCore\Entity\CampType::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation' => [
        'EcampApi\\V1\\Rest\\CampType\\Controller' => [
            'input_filter' => 'EcampApi\\V1\\Rest\\CampType\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'EcampApi\\V1\\Rest\\CampType\\Validator' => [
          
        ],
    ],
];
