<?php

return [
    'router' => [
        'routes' => [
            'e-camp-api.rest.doctrine.activity-content.textarea' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/content-type/textareas[/:textareaId]',
                    'defaults' => [
                        'controller' => \eCamp\ContentType\Textarea\Controller\TextareaController::class,
                    ],
                ],
            ],
        ],
    ],

    'api-tools-rest' => [
        \eCamp\ContentType\Textarea\Controller\TextareaController::class => [
            'listener' => \eCamp\ContentType\Textarea\Service\TextareaService::class,
            'controller_class' => \eCamp\ContentType\Textarea\Controller\TextareaController::class,
            'route_name' => 'e-camp-api.rest.doctrine.activity-content.textarea',
            'route_identifier_name' => 'textareaId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                // 3 => 'DELETE' // disallow deleting directly. Single entities should always be deleted via ActivityContent.
            ],
            'collection_http_methods' => [
                0 => 'GET',
                // 1 => 'POST', // disallow posting directly. Single entities should always be created via ActivityContent.
            ],
            'collection_query_whitelist' => [
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => \eCamp\ContentType\Textarea\Entity\Textarea::class,
            'collection_class' => \eCamp\ContentType\Textarea\Entity\TextareaCollection::class,
            'service_name' => 'Textarea',
        ],
    ],

    'api-tools-hal' => [
        'metadata_map' => [
            \eCamp\ContentType\Textarea\Entity\Textarea::class => [
                'route_identifier_name' => 'textareaId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content.textarea',
                'route_params' => [
                    'activityContentId' => function ($object) {
                        return $object->getActivityContent()->getId();
                    },
                ],
                'hydrator' => eCamp\ContentType\Textarea\Hydrator\TextareaHydrator::class,
                'max_depth' => 2,
            ],
            \eCamp\ContentType\Textarea\Entity\TextareaCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content/textarea',
                'is_collection' => true,
                'max_depth' => 0,
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            'ecamp_contenttype_textarea_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\ContentType\Textarea\Entity' => 'ecamp_contenttype_textarea_entities',
                ],
            ],
        ],
    ],
];
