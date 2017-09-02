<?php 

return [
	'router' => [
		'routes' => [
			'ecamp-api.rest.doctrine.image' => [
				'type' => 'Segment',
				'options' => [
					'route' => '/api/image[/:image_id]',
					'defaults' => [
						'controller' => 'EcampApi\\V1\\Rest\\Image\\Controller',
					],
				],
				'may_terminate' => true,
				'child_routes' => [
					'show' => [
						'type' => 'Literal',
						'options' => [
							'route' => '/show',
							'defaults' => [
								'controller' => 'EcampApi\\Controller\\AssetsController',
								'action' => 'apiV1Image'
							],
						],
						'may_terminate' => true,
					],
				],
			],
		],
	],
	'zf-versioning' => [
		'uri' => [
			0 => 'ecamp-api.rest.doctrine.image',
		],
	],
	'zf-rest' => [
		'EcampApi\\V1\\Rest\\Image\\Controller' => [
			'listener' => \EcampApi\V1\Rest\Image\ImageResource::class,
			'route_name' => 'ecamp-api.rest.doctrine.image',
			'route_identifier_name' => 'image_id',
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
			'entity_class' => \EcampCore\Entity\Image::class,
			'collection_class' => \EcampApi\V1\Rest\Image\ImageCollection::class,
			'service_name' => 'Image',
		],
	],
	'zf-content-negotiation' => [
		'controllers' => [
			'EcampApi\\V1\\Rest\\Image\\Controller' => 'HalJson',
		],
		'accept-whitelist' => [
			'EcampApi\\V1\\Rest\\Image\\Controller' => [
				0 => 'application/vnd.ecamp-api.v1+json',
				1 => 'application/hal+json',
				2 => 'application/json',
			],
		],
		'content-type-whitelist' => [
			'EcampApi\\V1\\Rest\\Image\\Controller' => [
				0 => 'application/json',
			],
		],
	],
	'zf-hal' => [
		'metadata_map' => [
			\EcampCore\Entity\Image::class => [
				'route_identifier_name' => 'image_id',
				'entity_identifier_name' => 'id',
				'route_name' => 'ecamp-api.rest.doctrine.image',
				'hydrator' => 'EcampApi\\V1\\Rest\\Image\\ImageHydrator',
				'links' => [
					[
						'rel' => 'show',
						'route' => [
							'name' => 'ecamp-api.rest.doctrine.image/show',
						]
					]
				],
			],
			\EcampApi\V1\Rest\Image\ImageCollection::class => [
				'entity_identifier_name' => 'id',
				'route_name' => 'ecamp-api.rest.doctrine.image',
				'is_collection' => true,
			],
		],
	],
	'zf-apigility' => [
		'doctrine-connected' => [
			\EcampApi\V1\Rest\Image\ImageResource::class => [
				'object_manager' => 'doctrine.entitymanager.orm_default',
				'hydrator' => 'EcampApi\\V1\\Rest\\Image\\ImageHydrator',
			],
		],
	],
	'doctrine-hydrator' => [
		'EcampApi\\V1\\Rest\\Image\\ImageHydrator' => [
			'entity_class' => \EcampCore\Entity\Image::class,
			'object_manager' => 'doctrine.entitymanager.orm_default',
			'by_value' => true,
			'strategies' => [],
			'use_generated_hydrator' => true,
		],
	],
	'zf-content-validation' => [
		'EcampApi\\V1\\Rest\\Image\\Controller' => [
			'input_filter' => 'EcampApi\\V1\\Rest\\Image\\Validator',
		],
	],
	'input_filter_specs' => [
		'EcampApi\\V1\\Rest\\Image\\Validator' => [],
	],
];
