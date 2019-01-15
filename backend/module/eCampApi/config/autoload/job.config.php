<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.job' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/job[/:job_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\JobApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Api\RestController\JobApiController::class => \ZF\Rest\Factory\RestControllerFactory::class
        ]
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\JobApiController::class => [
            'listener' => \eCamp\Core\EntityService\JobService::class,
            'controller_class' => \eCamp\Api\RestController\JobApiController::class,
            'route_name' => 'ecamp.api.job',
            'route_identifier_name' => 'job_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST'
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Core\Entity\Job::class,
            'collection_class' => \eCamp\Api\Collection\JobCollection::class,
            'service_name' => 'Job',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\Job::class => [
                'route_identifier_name' => 'job_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.job',
                'hydrator' => eCamp\Core\Hydrator\JobHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\JobCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.job',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
