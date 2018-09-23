<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.organization' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/organization[/:organization_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\OrganizationApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\OrganizationApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\OrganizationApiController::class => [
            'listener' => \eCamp\Core\EntityService\OrganizationService::class,
            'controller_class' => \eCamp\Api\RestController\OrganizationApiController::class,
            'route_name' => 'ecamp.api.organization',
            'route_identifier_name' => 'organization_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\Organization::class,
            'collection_class' => \eCamp\Api\Collection\OrganizationCollection::class,
            'service_name' => 'Organization',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Organization::class => [
                'route_identifier_name' => 'organization_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.organization',
                'hydrator' => eCamp\Core\Hydrator\OrganizationHydrator::class,
                'max_depth' => 1
            ],
            \eCamp\Api\Collection\OrganizationCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.organization',
                'is_collection' => true,
                'max_depth' => 1
            ],
        ],
    ],

];
