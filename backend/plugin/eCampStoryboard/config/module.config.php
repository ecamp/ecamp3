<?php

return [
    'router' => [
        'routes' => [
            'ecamp.api.event_plugin' => [
                'child_routes' => [
                    'ecamp.section' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/section[/:section_id]',
                            'defaults' => [
                                'controller' => \eCamp\Plugin\Storyboard\Controller\SectionController::class,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'move' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/:action',
                                    'defaults' => [
                                        'controller' => \eCamp\Plugin\Storyboard\Controller\SectionActionController::class,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            \eCamp\Plugin\Storyboard\Controller\SectionActionController::class => \eCamp\Plugin\Storyboard\Controller\SectionActionControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            \eCamp\Plugin\Storyboard\Strategy::class => \eCamp\Plugin\Storyboard\StrategyFactory::class,
            \eCamp\Plugin\Storyboard\Service\SectionService::class => \eCamp\Core\Plugin\DefaultPluginServiceFactory::class,
        ],
    ],

    'zf-rest' => [
        \eCamp\Plugin\Storyboard\Controller\SectionController::class => [
            'listener' => \eCamp\Plugin\Storyboard\Service\SectionService::class,
            'controller_class' => \eCamp\Plugin\Storyboard\Controller\SectionController::class,
            'route_name' => 'ecamp.api.event_plugin/ecamp.section',
            'route_identifier_name' => 'section_id',
            'entity_identifier_name' => 'id',
            //'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [],
            //'page_size' => 25,
            //'page_size_param' => null,
            'entity_class' => \eCamp\Plugin\Storyboard\Entity\Section::class,
            'collection_class' => \eCamp\Plugin\Storyboard\Entity\SectionCollection::class,
            'service_name' => 'Section',
        ],
    ],

    'zf-hal' => [
        'metadata_map' => [
            \eCamp\Plugin\Storyboard\Entity\Section::class => [
                'route_identifier_name' => 'section_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_plugin/ecamp.section',
                'hydrator' => eCamp\Plugin\Storyboard\Hydrator\SectionHydrator::class,
                'max_depth' => 2,
            ],
            \eCamp\Plugin\Storyboard\Entity\SectionCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'ecamp.api.event_plugin/ecamp.section',
                'is_collection' => true,
                'max_depth' => 0,
            ],
        ],
    ],

    'doctrine' => [
        'driver' => [
            'ecamp_plugin_storyboard_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__.'/../src/Entity'],
            ],

            'orm_default' => [
                'drivers' => [
                    'eCamp\Plugin\Storyboard\Entity' => 'ecamp_plugin_storyboard_entities',
                ],
            ],
        ],
    ],
];
