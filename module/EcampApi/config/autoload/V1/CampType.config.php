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
	'service_manager' => [
		'factories' => [
			\EcampApi\V1\Rest\CampType\CampTypeHydrator::class => \EcampApi\V1\DoctrineHydratorFactory::class,
				\EcampApi\V1\Rest\CampType\CampTypeFilter::class => \Zend\ServiceManager\Factory\InvokableFactory::class
		]
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
            	'hydrator' => \EcampApi\V1\Rest\CampType\CampTypeHydrator::class,
            	'max_depth' => 2
            ],
            \EcampApi\V1\Rest\CampType\CampTypeCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp-api.rest.doctrine.camp-type',
                'is_collection' => true,
            	'max_depth' => 1
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \EcampApi\V1\Rest\CampType\CampTypeResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
            	'hydrator' => \EcampApi\V1\Rest\CampType\CampTypeHydrator::class,
            ],
        ],
    ],
    'doctrine-hydrator' => [
    	\EcampApi\V1\Rest\CampType\CampTypeHydrator::class=> [
            'entity_class' => \EcampCore\Entity\CampType::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
        	'hydrator' => \EcampApi\V1\Rest\CampType\CampTypeHydrator::class,
        	'by_value' => true,
        	'strategies' => [
        		//'eventTypes' => ZF\Doctrine\Hydrator\Strategy\CollectionExtract::class
        	],
        	'filters' => [
        		[ 'condition' => 'and', 'filter' => \EcampApi\V1\BaseEntityFilter::class ],
        		[ 'condition' => 'and', 'filter' => \EcampApi\V1\Rest\CampType\CampTypeFilter::class ]
        	]
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
