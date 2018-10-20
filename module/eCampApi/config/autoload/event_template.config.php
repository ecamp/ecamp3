<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_template' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_template[/:event_template_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventTemplateApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\EventTemplateApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventTemplateApiController::class => [
            'listener' => \eCamp\Core\EntityService\EventTemplateService::class,
            'controller_class' => \eCamp\Api\RestController\EventTemplateApiController::class,
            'route_name' => 'ecamp.api.event_template',
            'route_identifier_name' => 'event_template_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [
                'event_id',
                'event_category_id',
                'event_type_id',
                'medium'
            ],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\EventTemplate::class,
            'collection_class' => \eCamp\Api\Collection\EventTemplateCollection::class,
            'service_name' => 'EventTemplate',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventTemplate::class => [
                'route_identifier_name' => 'event_template_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_template',
                'hydrator' => eCamp\Core\Hydrator\EventTemplateHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventTemplateCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_template',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
