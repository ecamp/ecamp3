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
                                '__NAMESPACE__' => 'EcampStoryboard\Controller',
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
                                        'controller' => 'Sections',
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
                                        'controller' => 'Index',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EcampStoryboard\Controller\Sections' => 'EcampStoryboard\Controller\SectionsController',
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
