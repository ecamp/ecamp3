<?php
return array(
    'type'    => 'Segment',
    'options' => array(
        'route'    => '/web[/:locale]',
        'defaults' => array(
            'module'        => 'EcampWeb',
            'controller'    => 'Index',
            'action'        => 'index',
            'locale'        => 'en',
            '__NAMESPACE__' => 'EcampWeb\Controller',
        ),
        'constraints' => array(
            'locale' => '[a-z]{2}',
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
                    'action' => 'login',
                )
            )
        ),

        'register' => array(
            'type' => 'Segment',
            'options' => array(
                'route' => '/register[/:action]',
                'defaults' => array(
                    '__NAMESPACE__' => 'EcampWeb\Controller\Auth',
                    'controller' => 'Register' ,
                    'action' => 'register',
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

        'camp' => array(
            'type'    => 'Segment',
            'options' => array(
                'route'    => '/camp/:camp/:controller[/:action]',
                'constraints' => array(
                    'camp'       => '[a-f0-9]+',
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'module'        => 'EcampWeb',
                    '__NAMESPACE__' => 'EcampWeb\Controller\Camp',
                    'action'    	=> 'index',
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
);
