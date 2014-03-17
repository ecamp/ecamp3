<?php
return array(
    'router' => array(
        'routes' => array(
            'web' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/web',
                    'defaults' => array(
                        'controller'    => 'Index',
                        'action'        => 'index',
                        '__NAMESPACE__' => 'EcampWeb\Controller',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(

                    'default' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                        'route'    => '/[:controller[/:action]]',
                                        'constraints' => array(
                                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        ),
                                ),
                    ),

                    'group-prefix' => array(
                            'type'    => 'Literal',
                            'options' => array(
                                    'route'    => '/group',
                                    'defaults' => array(
                                            '__NAMESPACE__' => 'EcampWeb\Controller\Group',
                                            'controller'    => 'Index',
                                            'action'    	=> 'index',
                                    ),
                            ),
                            'may_terminate' => false,
                            'child_routes' => array(
                                    'name' => array(
                                                    'type' => 'EcampCore\Router\GroupRouter',
                                                    'may_terminate' => true,

                                                    'child_routes' => array(

                                                            'default' => array(
                                                                    'type'    => 'Segment',
                                                                    'options' => array(
                                                                            'route'    => '[/:controller[/:action]]',
                                                                            'constraints' => array(
                                                                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                    ),

                                    'name+camp' => array(
                                            'type' => 'EcampCore\Router\GroupCampRouter',
                                            'may_terminate' => true,
                                            'options' => array(
                                                    'defaults' => array(
                                                            '__NAMESPACE__' => 'EcampWeb\Controller\Camp',
                                                            'controller'    => 'Index',
                                                            'action'     	=> 'index',
                                                    ),
                                            ),
                                            'child_routes' => array(
                                                    'default' => array(
                                                            'type'    => 'Segment',
                                                            'options' => array(
                                                                    'route'    => '[/:controller[/:action]]',
                                                                    'constraints' => array(
                                                                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                            'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                            ),
                    ),

                    'user-prefix' => array(
                            'type'    => 'Literal',
                            'options' => array(
                                    'route'    => '/user',
                                    'defaults' => array(
                                            '__NAMESPACE__' => 'EcampWeb\Controller\User',
                                            'controller'    => 'Index',
                                            'action'    	=> 'index',
                                    ),
                            ),
                            'may_terminate' => false,
                            'child_routes' => array(
                                    'name' => array(
                                                    'type' => 'EcampCore\Router\UserRouter',
                                                    'may_terminate' => true,

                                                    'child_routes' => array(

                                                            'default' => array(
                                                                    'type'    => 'Segment',
                                                                    'options' => array(
                                                                            'route'    => '[/:controller[/:action]]',
                                                                            'constraints' => array(
                                                                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                    ),

                                    'name+camp' => array(
                                            'type' => 'EcampCore\Router\UserCampRouter',
                                            'may_terminate' => true,
                                            'options' => array(
                                                    'defaults' => array(
                                                            '__NAMESPACE__' => 'EcampWeb\Controller\Camp',
                                                            'controller'    => 'Index',
                                                            'action'     	=> 'index',
                                                    ),
                                            ),
                                            'child_routes' => array(
                                                    'default' => array(
                                                            'type'    => 'Segment',
                                                            'options' => array(
                                                                    'route'    => '[/:controller[/:action]]',
                                                                    'constraints' => array(
                                                                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                            'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                            ),
                    ),

                    'plugin-create' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/plugin/create/:eventId/:pluginId',
                            'constraints' => array(
                                'eventId' => '[a-f0-9]+',
                                'pluginId' => '[a-f0-9]+',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampWeb\Controller',
                                'controller' => 'EventPlugin',
                                'action'     => 'create',
                            ),
                        ),
                        'may_terminate' => true,
                    ),

                    'plugin-get' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/plugin/get/:eventPluginId',
                            'constraints' => array(
                                'eventPluginId' => '[a-f0-9]+'
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampWeb\Controller',
                                'controller' => 'EventPlugin',
                                'action'     => 'get',
                            ),
                        ),
                        'may_terminate' => true,
                    ),

                    'plugin-delete' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/plugin/delete/:eventPluginId',
                            'constraints' => array(
                                'eventPluginId' => '[a-f0-9]+'
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampWeb\Controller',
                                'controller' => 'EventPlugin',
                                'action'     => 'delete',
                            ),
                        ),
                        'may_terminate' => true,
                    ),

                ),
            ),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
//		'not_found_template'       => 'error/404',
//		'exception_template'       => 'error/index',
        'layout'                   => 'layout/layout',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.twig',
//			'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
//			'error/404'               => __DIR__ . '/../view/error/404.phtml',
//			'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'zfctwig' => array(
        'environment_options' => array(
            //'cache' => 'data/cache',
        )
    ),
);
