<?php
return array(

    'router' => array(
        'routes' => array(
            'plugin' => array(
                'child_routes' => array(

                    'material' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/material',
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampMaterial\Controller',
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
                                        'controller' => 'Index',
                                    ),
                                ),
                            ),
                            'dictionary' => array(
                                        'type'    => 'Segment',
                                        'options' => array(
                                                'route'    => '/dictionary[/:query]',
                                                'defaults' => array(
                                                        'controller' => 'Dictionary',
                                                        'action'     => 'index',
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
            'EcampMaterial\Controller\Item' => 'EcampMaterial\Controller\ItemController',
            'EcampMaterial\Controller\Dictionary' => 'EcampMaterial\Controller\DictionaryController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_material_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampMaterial/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampMaterial\Entity' => 'ecamp_material_entities'
                )
            )
        ),

    ),

);
