<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_template_container' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event_template_container[/:event_template_container_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\EventTemplateContainerApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\EventTemplateContainerApiController::class => [
            'listener' => \eCamp\Core\EntityService\EventTemplateContainerService::class,
            'controller_class' => \eCamp\Api\RestController\EventTemplateContainerApiController::class,
            'route_name' => 'ecamp.api.event_template_container',
            'route_identifier_name' => 'event_template_container_id',
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
            'entity_class' => \eCamp\Core\Entity\EventTemplateContainer::class,
            'collection_class' => \eCamp\Api\Collection\EventTemplateContainerCollection::class,
            'service_name' => 'EventTemplateContainer',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\EventTemplateContainer::class => [
                'route_identifier_name' => 'event_template_container_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_template_container',
                'hydrator' => eCamp\Core\Hydrator\EventTemplateContainerHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\EventTemplateContainerCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_template_container',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
