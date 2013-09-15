<?php
return array(
    'router' => array(
        'routes' => array(
            'dev' => array(
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

                    'group' => array(
                            'type'    => 'Literal',
                            'options' => array(
                                    'route'    => '/group',
                                    'defaults' => array(
                                            'controller'    => 'Group',
                                            'action'     => 'index',
                                    ),
                            ),
                            'may_terminate' => false,
                            'child_routes' => array(
                                    /*'dev' => array(
                                            'type'    => 'Literal',
                                            'options' => array(
                                                    'route'    => '/pbs',
                                                    'defaults' => array(
                                                            'controller'    => 'Group',
                                                            'action'        => 'index',
                                                    ),
                                            ),
                                    ),*/

                                    'groupname' => array(
                                                    'type' => 'EcampCore\Router\GroupRouter',
                                                    'may_terminate' => true,
                                    )
                            ),
                    ),

                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EcampWeb\Controller\Index' => 'EcampWeb\Controller\IndexController',
            'Index' => 'EcampWeb\Controller\IndexController',
            'Camp'  => 'EcampWeb\Controller\CampController',
            'Group'  => 'EcampWeb\Controller\GroupController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
