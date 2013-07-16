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
            'EcampApi\Controller\Contributors' 		=> 'EcampApi\Controller\ContributorsController',
            'EcampApi\Controller\Camps' 			=> 'EcampApi\Controller\CampsController',
            'EcampApi\Controller\Periods' 			=> 'EcampApi\Controller\PeriodsController',
            'EcampApi\Controller\Days' 				=> 'EcampApi\Controller\DaysController',
            'EcampApi\Controller\EventInstances'	=> 'EcampApi\Controller\EventInstancesController',
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
