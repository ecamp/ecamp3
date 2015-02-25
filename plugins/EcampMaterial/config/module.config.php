<?php
return array(
        'router' => array(
                'routes' => array(
                        'api-material' => array(
                                'type' => 'Literal',
                                'options' => array(
                                        'route' => '/api/plugin/material/v0',
                                        'defaults' => array(
                                                '__NAMESPACE__' => 'EcampMaterial'
                                        )
                                ),

                                'may_terminate' => false,
                                'child_routes' => array(

                                        'items' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/:eventPlugin/items[/:item]',
                                                        'defaults' => array(
                                                                'controller'    => 'Resource\MaterialItem\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,

                                        ),

                                        'lists' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/:eventPlugin/lists[/:list]',
                                                        'defaults' => array(
                                                                'controller'    => 'Resource\MaterialList\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,

                                        ),

                                ),
                        ),

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
            'EcampMaterial\Controller\Dictionary' => 'EcampMaterial\Controller\DictionaryController',
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

        'phlyrestfully' => array(
                'resources' => array(

                        'EcampMaterial\Resource\MaterialItem\ApiController' => array(
                                'listener'                => 'EcampMaterial\Resource\MaterialItem\MaterialItemResourceListener',
                                'collection_http_options' => array('get'),
                                'page_size'               => 3,
                                'page_size_param'		  => 'limit',
                                'resource_http_options'   => array('get', 'put'),
                                'route_name'              => 'api-material/items',
                                'identifier_name'		  => 'item'
                        ),

                        'EcampMaterial\Resource\MaterialList\ApiController' => array(
                                'listener'                => 'EcampMaterial\Resource\MaterialList\MaterialListResourceListener',
                                'collection_http_options' => array('get'),
                                'page_size'               => 3,
                                'page_size_param'		  => 'limit',
                                'resource_http_options'   => array('get'),
                                'route_name'              => 'api-material/lists',
                                'identifier_name'		  => 'list'
                        ),

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
