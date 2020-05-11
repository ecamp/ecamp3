<?php

return [
    'router' => [
        'routes' => [
            'e-camp-api.rest.doctrine.activity-content.storyboard' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/content-type/storyboards[/:sectionId]',
                    'defaults' => [
                        'controller' => \eCamp\ContentType\Storyboard\Controller\SectionController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'move' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/:action',
                            'defaults' => [
                                'controller' => \eCamp\ContentType\Storyboard\Controller\SectionActionController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'zf-rest' => [
        \eCamp\ContentType\Storyboard\Controller\SectionController::class => [
            'listener' => \eCamp\ContentType\Storyboard\Service\SectionService::class,
            'controller_class' => \eCamp\ContentType\Storyboard\Controller\SectionController::class,
            'route_name' => 'e-camp-api.rest.doctrine.activity-content.storyboard',
            'route_identifier_name' => 'sectionId',
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
                0 => 'activityContentId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => \eCamp\ContentType\Storyboard\Entity\Section::class,
            'collection_class' => \eCamp\ContentType\Storyboard\Entity\SectionCollection::class,
            'service_name' => 'Section',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\ContentType\Storyboard\Entity\Section::class => [
                'route_identifier_name' => 'sectionId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content.storyboard',
                'route_params' => [
                    'activityContentId' => function ($object) {
                        return $object->getActivityContent()->getId();
                    },
                ],
                'hydrator' => eCamp\ContentType\Storyboard\Hydrator\SectionHydrator::class,
                'max_depth' => 2,
            ],
            \eCamp\ContentType\Storyboard\Entity\SectionCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content.storyboard',
                'is_collection' => true,
                'max_depth' => 0,
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            'ecamp_contenttype_storyboard_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\ContentType\Storyboard\Entity' => 'ecamp_contenttype_storyboard_entities',
                ],
            ],
        ],
    ],
];
