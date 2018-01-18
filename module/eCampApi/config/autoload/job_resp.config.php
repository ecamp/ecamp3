<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.job_resp' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/job_resp[/:job_resp_id]',
                    'defaults' => [
                        'controller' => \eCamp\Api\RestController\JobRespApiController::class,
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\Api\RestController\JobRespApiController::class => [
            'listener' => \eCamp\Core\Service\JobRespService::class,
            'controller_class' => \eCamp\Api\RestController\JobRespApiController::class,
            'route_name' => 'ecamp.api.job_resp',
            'route_identifier_name' => 'job_resp_id',
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
            'entity_class' => \eCamp\Core\Entity\JobResp::class,
            'collection_class' => \eCamp\Api\Collection\JobRespCollection::class,
            'service_name' => 'JobResp',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Core\Entity\JobResp::class => [
                'route_identifier_name' => 'job_resp_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.job_resp',
                'hydrator' => eCamp\Core\Hydrator\JobRespHydrator::class,
                'max_depth' => 2
            ],
            \eCamp\Api\Collection\JobRespCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.job_resp',
                'is_collection' => true,
                'max_depth' => 0
            ],
        ],
    ],

];
