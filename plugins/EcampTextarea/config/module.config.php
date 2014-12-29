<?php
return array(

    'router' => array(
        'routes' => array(
            'plugin' => array(
                'child_routes' => array(

                    'textarea' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/textarea',
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampTextarea',
                            ),
                        ),

                        'may_terminate' => true,
                        'child_routes' => array(
                            'api' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route'      => '/textarea[/:textarea]',
                                    'defaults' => array(
                                        'controller' => 'Resource\Textarea\ApiController'
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'phlyrestfully' => array(
        'resources' => array(

            /**
             * Textarea
             */
            'EcampTextarea\Resource\Textarea\ApiController' => array(
                'listener'                => 'EcampTextarea\Resource\TextareaResourceListener',
                'collection_http_options' => array('get'),
                'page_size'               => PHP_INT_MAX,
                'page_size_param'		  => 'limit',
                'resource_http_options'   => array('get', 'put'),
                'route_name'              => 'plugin/textarea/api',
                'identifier_name'		  => 'textarea',
                'collection_query_whitelist' => array(),
            ),
        ),
    ),

    'controllers' => array(

    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_textarea_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampTextarea/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampTextarea\Entity' => 'ecamp_textarea_entities'
                )
            )
        ),

    ),

);
