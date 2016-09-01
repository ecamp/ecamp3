<?php
return array(
    'router' => array(
        'routes' => array(
            'api-courseaim' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/plugin/courseaim/v0',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EcampCourseAim'
                    )
                ),

                'may_terminate' => false,
                'child_routes' => array(

                    'items' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'      => '/:eventPlugin/items/:item',
                            'defaults' => array(
                                'controller'    => 'Resource\Item\ApiController'
                            ),
                        ),
                        'may_terminate' => true,

                    ),
                ),
            ),
        ),
    ),

    'phlyrestfully' => array(
        'resources' => array(

            'EcampCourseAim\Resource\Item\ApiController' => array(
                'listener'                => 'EcampCourseAim\Resource\Item\ItemResourceListener',
                'collection_http_options' => array('get'),
                'page_size'               => 3,
                'page_size_param'		  => 'limit',
                'resource_http_options'   => array('put', 'delete'),
                'route_name'              => 'api-courseaim/items',
                'identifier_name'		  => 'item'
            )

        ),

    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
                'ViewJsonStrategy',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_courseaim_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampCourseAim/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampCourseAim\Entity' => 'ecamp_courseaim_entities'
                )
            )
        ),

    ),

    'ecamp' => array(
        'doctrine' => array(
            'repository' => array(
                'ecamp_courseaim' => array(
                    'entitymanager' => 'orm_default',
                    'mappings' => array(
                        "/^EcampCourseAim\\\\Repository\\\\(\\w+)$/" => "EcampCourseAim\\\\Entity\\\\$1",
                    ),
                ),
            ),
        ),


    ),

);
