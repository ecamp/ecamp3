<?php
return array(

    'router' => array(
        'routes' => array(
            'plugin' => array(
                'child_routes' => array(

                    'storyboard' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/storyboard',
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampStoryboard',
                            ),
                        ),

                        'may_terminate' => false,
                        'child_routes' => array(
                            'default' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:action[/:id]]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id'		 => '[a-f0-9]+'
                                    ),
                                    'defaults' => array(
                                        '__NAMESPACE__' => 'EcampStoryboard\Controller',
                                        'controller' => 'Item',
                                        'action'     => 'index',
                                    ),
                                ),
                            ),
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'id' 		 => '[a-f0-9]+'
                                    ),
                                    'defaults' => array(
                                        '__NAMESPACE__' => 'EcampStoryboard\Controller',
                                        'controller' => 'Index',
                                    ),
                                ),
                            ),
                            'api' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route'      => '/sections[/:section]',
                                    'defaults' => array(
                                        'controller' => 'Resource\Section\ApiController'
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
             * Section
             */
            'EcampStoryboard\Resource\Section\ApiController' => array(
                'listener'                => 'EcampStoryboard\Resource\SectionResourceListener',
                'collection_http_options' => array('get'),
                'page_size'               => PHP_INT_MAX,
                'page_size_param'		  => 'limit',
                'resource_http_options'   => array('get', 'put'),
                'route_name'              => 'plugin/storyboard/api',
                'identifier_name'		  => 'section',
                'collection_query_whitelist' => array(),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EcampStoryboard\Controller\Item' => 'EcampStoryboard\Controller\ItemController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_storyboard_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampStoryboard/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampStoryboard\Entity' => 'ecamp_storyboard_entities'
                )
            )
        ),

    ),

);
