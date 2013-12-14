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

                    'login' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/:action]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'EcampWeb\Controller\Auth',
                                'controller' => 'Login' ,
                                'action' => 'index',
                            )
                        )
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

                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    )
);
