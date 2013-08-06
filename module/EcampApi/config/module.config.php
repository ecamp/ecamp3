<?php
return array(
    'router' => array(
        'routes' => array(
        		
        	'api' => array(
        						'type' => 'Literal',
        						'options' => array(
        								'route' => '/api/v0'
        						),
        						'may_terminate' => false,
        						'child_routes' => array(
        								'camps' => array(
        										'type' => 'Segment',
        										'options' => array(
        												'route'      => '/camps[/:camp]',
        												'defaults' => array(
                       										'controller'    => 'EcampApi\Camp\ApiController'
        												),
        										),
        								),
        								
        								'users' => array(
        										'type' => 'Segment',
        										'may_terminate' => true,
        										'options' => array(
        												'route'      => '/users[/:user]',
        												'defaults' => array(
        														'controller'    => 'EcampApi\User\ApiController'
        												),
        										),
        										'child_routes' => array(
        												'camps' => array(
        														'type' => 'Segment',
        														'options' => array(
        																'route'      => '/camps',
        																'defaults' => array(
        																		'controller'    => 'EcampApi\User\Camp\ApiController'
        																),
        														),
        												)
        										)
        								),
        						),
        				),
        	
        	/*
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
            ),*/
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
    ),
    
    'phlyrestfully' => array(
    		
    		'resources' => array(
    				'EcampApi\Camp\ApiController' => array(
    						'listener'                => 'EcampApi\Camp\CampResourceListener',
    						'collection_http_options' => array('get'),
    						'page_size'               => 3,
    						'page_size_param'		  => 'limit',
    						'resource_http_options'   => array('get'),
    						'route_name'              => 'api/camps',
    						'identifier_name'		  => 'camp',
    						'collection_query_whitelist' => array('user', 'past')   /* to be discussed */
    				),
    				
    				'EcampApi\User\Camp\ApiController' => array(
    						'listener'                => 'EcampApi\Camp\CampResourceListener',
    						'collection_http_options' => array('get'),
    						'page_size'               => 3,
    						'page_size_param'		  => 'limit',
    						'resource_http_options'   => array('get'),
    						'route_name'              => 'api/users/camps',
    						'identifier_name'		  => 'camp',
    						'collection_query_whitelist' => array('past')   /* to be discussed */
    				),
    				
    				'EcampApi\User\ApiController' => array(
    						'listener'                => 'EcampApi\User\UserResourceListener',
    						'collection_http_options' => array('get'),
    						'page_size'               => 3,
    						'page_size_param'		  => 'limit',
    						'resource_http_options'   => array('get'),
    						'route_name'              => 'api/users',
    						'identifier_name'		  => 'user'
    				),
    		),
    		
    ),
);
