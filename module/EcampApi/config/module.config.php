<?php
return array(
    'router' => array(
        'routes' => array(
            'api' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/api',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'EcampApi\Controller',
                        'controller'    => 'Index',
                    ),
                ),

                'may_terminate' => true,
                'child_routes' => array(
                    'rest' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:id][.:format]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'format' => '(xml|json)',
                                'id' => '[a-f0-9]*'
                            ),
                            'defaults' => array(
                                'controller' => 'Index',
                                'format' => 'json'
                            ),
                        ),
                    ),

                    'user' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/users/:user',
                            'constraints' => array(
                                   'user' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'collaboration' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/collaborations/:collaboration',
                            'constraints' => array(
                                   'collaboration' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'camp' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/camps/:camp',
                            'constraints' => array(
                                   'camp' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'period' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/periods/:period',
                            'constraints' => array(
                                   'period' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'day' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/days/:day',
                            'constraints' => array(
                                   'day' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'event' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/events/:event',
                            'constraints' => array(
                                   'event' => '[a-f0-9]*'
                            ),
                        ),
                        'child_routes' => array(
                            'rest' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/[:controller[/:id][.:format]]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'format' => '(xml|json)',
                                        'id' => '[a-f0-9]*'
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Index',
                                        'format' => 'json'
                                    ),
                                ),
                            ),
                        )
                    ),

                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/login[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Login',
                                'action' => 'Index'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'EcampApi\Controller\Login' 			=> 'EcampApi\Controller\LoginController',
            'EcampApi\Controller\Index' 			=> 'EcampApi\Controller\IndexController',
            'EcampApi\Controller\Users' 			=> 'EcampApi\Controller\UsersController',
            'EcampApi\Controller\Collaborations'	=> 'EcampApi\Controller\CollaborationsController',
            'EcampApi\Controller\Camps' 			=> 'EcampApi\Controller\CampsController',
            'EcampApi\Controller\Periods' 			=> 'EcampApi\Controller\PeriodsController',
            'EcampApi\Controller\Days' 				=> 'EcampApi\Controller\DaysController',
            'EcampApi\Controller\EventCategories'	=> 'EcampApi\Controller\EventCategoriesController',
            'EcampApi\Controller\EventInstances'	=> 'EcampApi\Controller\EventInstancesController',
            'EcampApi\Controller\EventResps'		=> 'EcampApi\Controller\EventRespsController',
            'EcampApi\Controller\Events' 			=> 'EcampApi\Controller\EventsController',
        ),
    ),

    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'display_exceptions' => false
    )
);
