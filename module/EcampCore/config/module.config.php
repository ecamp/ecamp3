<?php
return array(
	'ecamp' => array(
		'modules' => array(
			'core' => array(
				'repos' => array(
					'module_namespace' => 'EcampCore',
					'config_file' => __DIR__ . '/service.config.repos.php',
				),
				
				'services' => array(
					'services_path' => __DIR__ . '/../src/EcampCore/Service/',
					'config_file' => __DIR__ . '/service.config.services.php',
				),
			)
		)
	),
	
    'router' => array(
        'routes' => array(
        	
            'plugin' => array(
            	'type'    => 'Segment',
            	'options' => array(
            		'route'    => '/plugin/:pluginInstanceId',
            		'constraints' => array(
            			'pluginInstanceId' => '[a-f0-9]+'
            		),
            	),
            	'may_terminate' => false,
            ),
        	
        	'core' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/core',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EcampCore\Controller',
                        'controller'    => 'Index',
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
                ),
            ),
		),
	),
	
    'controllers' => array(
        'invokables' => array(
            'EcampCore\Controller\Index' 	=> 'EcampCore\Controller\IndexController',
            'EcampCore\Controller\Login'	=> 'EcampCore\Controller\LoginController',
            'EcampCore\Controller\Event'	=> 'EcampCore\Controller\EventController',
        ),
    ),
    
	'view_manager' => array(
        'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
	
	'doctrine' => array(
		'driver' => array(
			'ecamp_entities' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/EcampCore/Entity')
			),
			
			'orm_default' => array(
				'drivers' => array(
					'EcampCore\Entity' => 'ecamp_entities'
				)
			)
		),
		
		'configuration' => array(
			'orm_default' => array(
				'filters' => array(
					'user' 			=> 'EcampCore\DbFilter\UserFilter',
					'login' 		=> 'EcampCore\DbFilter\LoginFilter', 
					
					'usercamp' 		=> 'EcampCore\DbFilter\UserCampFilter',
					
					'camp' 			=> 'EcampCore\DbFilter\CampFilter',
					'period' 		=> 'EcampCore\DbFilter\PeriodFilter',
					'day' 			=> 'EcampCore\DbFilter\DayFilter',
					
					'event' 		=> 'EcampCore\DbFilter\EventFilter',
					'eventinstance' => 'EcampCore\DbFilter\EventInstanceFilter',
					
				)
			)
		)
	)
);