<?php

return [
	
		'router' => [
				'routes' => [
						'ecamp-api.rest.doctrine.period' => [
								'type' => 'Segment',
								'options' => [
										'route' => '/api/period[/:period_id]',
										'defaults' => [
												'controller' => 'EcampApi\\V1\\Rest\\Period\\Controller',
										],
								],
						],
				],
		],
		'zf-versioning' => [
				'uri' => [
						0 => 'ecamp-api.rest.doctrine.period',
				],
		],
		'zf-rest' => [
				'EcampApi\\V1\\Rest\\Period\\Controller' => [
                    'listener' => \EcampCore\Service\PeriodService::class,
                    'route_name' => 'ecamp-api.rest.doctrine.period',
                    'route_identifier_name' => 'period_id',
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
                    'entity_class' => \EcampCore\Entity\Period::class,
                    'collection_class' => \EcampApi\V1\Rest\Period\PeriodCollection::class,
                    'service_name' => 'Period',

				],
                'EcampApi\\V1\\Rest\\Period\\Controller::DUMMMMMMMMMMMMY' => [
                    'listener' => \EcampApi\V1\Rest\Period\PeriodResource::class,
                    'route_identifier_name' => 'period_id',
                    'entity_identifier_name' => 'id',
                ],
		],
		'zf-content-negotiation' => [
				'controllers' => [
						'EcampApi\\V1\\Rest\\Period\\Controller' => 'HalJson',
				],
				'accept-whitelist' => [
						'EcampApi\\V1\\Rest\\Period\\Controller' => [
								0 => 'application/vnd.ecamp-api.v1+json',
								1 => 'application/hal+json',
								2 => 'application/json',
						],
				],
				'content-type-whitelist' => [
						'EcampApi\\V1\\Rest\\Period\\Controller' => [
								0 => 'application/json',
						],
				],
		],
		'zf-hal' => [
				'metadata_map' => [
						\EcampCore\Entity\Period::class => [
								'route_identifier_name' => 'period_id',
								'entity_identifier_name' => 'id',
								'route_name' => 'ecamp-api.rest.doctrine.period',
								'hydrator' => 'EcampApi\\V1\\Rest\\Period\\PeriodHydrator',
						],
						\EcampApi\V1\Rest\Period\PeriodCollection::class => [
								'entity_identifier_name' => 'id',
								'route_name' => 'ecamp-api.rest.doctrine.period',
								'is_collection' => true,
						],
				],
		],
		'zf-apigility' => [
				'doctrine-connected' => [
						\EcampApi\V1\Rest\Period\PeriodResource::class => [
								'object_manager' => 'doctrine.entitymanager.orm_default',
								'hydrator' => 'EcampApi\\V1\\Rest\\Period\\PeriodHydrator',
						],
				],
		],
		'doctrine-hydrator' => [
				'EcampApi\\V1\\Rest\\Period\\PeriodHydrator' => [
						'entity_class' => \EcampCore\Entity\Period::class,
						'object_manager' => 'doctrine.entitymanager.orm_default',
						'by_value' => true,
						'strategies' => [],
						'use_generated_hydrator' => true,
				],
		],
		'zf-content-validation' => [
				'EcampApi\\V1\\Rest\\Period\\Controller' => [
						'input_filter' => 'EcampApi\\V1\\Rest\\Period\\Validator',
				],
		],
		'input_filter_specs' => [
				'EcampApi\\V1\\Rest\\Period\\Validator' => [
						
						[
								'name' => 'end',
								'required' => false,
								'filters' => [],
								'validators' => [],
						],
						
						[
								'name' => 'start',
								'required' => true,
								'filters' => [],
								'validators' => [],
						],
						[
								'name' => 'description',
								'required' => false,
								'filters' => [
										0 => [
												'name' => \Zend\Filter\StringTrim::class,
										],
										1 => [
												'name' => \Zend\Filter\StripTags::class,
										],
								],
								'validators' => [
										0 => [
												'name' => \Zend\Validator\StringLength::class,
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
