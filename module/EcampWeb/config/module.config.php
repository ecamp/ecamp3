<?php
return array(
    'router' => array(
        'routes' => array(
            'web' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/web',
                    'defaults' => array(
                        'controller'    => 'EcampWeb\Controller\Index',
                        'action'        => 'index',
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

                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EcampWeb\Controller\Index' => 'EcampWeb\Controller\IndexController',
            'EcampWeb\Controller\Camp\Index'  => 'EcampWeb\Controller\Camp\IndexController',
            'EcampWeb\Controller\Group\Index'  => 'EcampWeb\Controller\Group\IndexController',
            'EcampWeb\Controller\Group\Member'  => 'EcampWeb\Controller\Group\MemberController',
            'EcampWeb\Controller\Bypass' => 'EcampWeb\Controller\BypassController',

        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
