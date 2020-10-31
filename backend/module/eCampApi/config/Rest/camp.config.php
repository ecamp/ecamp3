<?php

return [
    'router' => [
        'routes' => [
            'e-camp-api.rest.doctrine.camp' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camps[/:campId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Camp\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'api-tools-rest' => [
        'eCampApi\\V1\\Rest\\Camp\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\CampService',
            'route_name' => 'e-camp-api.rest.doctrine.camp',
            'route_identifier_name' => 'campId',
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
            'collection_query_whitelist' => [
                0 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Camp',
            'collection_class' => 'eCampApi\\V1\\Rest\\Camp\\CampCollection',
            'service_name' => 'Camp',
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers' => [
            'eCampApi\\V1\\Rest\\Camp\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'eCampApi\\V1\\Rest\\Camp\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'eCampApi\\V1\\Rest\\Camp\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'api-tools-hal' => [
        'metadata_map' => [
            'eCamp\\Core\\Entity\\Camp' => [
                'route_identifier_name' => 'campId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Camp\\CampCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'is_collection' => true,
                'max_depth' => 20,
            ],
        ],
    ],
    'api-tools-content-validation' => [
        'eCampApi\\V1\\Rest\\Camp\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Camp\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'eCampApi\\V1\\Rest\\Camp\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 32,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'title',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 10,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'motto',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
