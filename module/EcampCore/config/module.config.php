<?php
return array(
	'ecamp' => array(
		'modules' => array(
			'ecampcore' => array(
				'repos' => array(
					'module_namespace' 	=> 'EcampCore',
					'config_file' 		=> __DIR__ . '/service.config.repos.php',
					'traits_path' 		=> __DIR__ . '/../src/EcampCore/RepositoryTraits/',
					'traits_namespace'	=> 'EcampCore\RepositoryTraits'
				),
				
				'services' => array(
					'services_path' 	=> __DIR__ . '/../src/EcampCore/Service/',
					'config_file' 		=> __DIR__ . '/service.config.services.php',
					'traits_path' 		=> __DIR__ . '/../src/EcampCore/ServiceTraits/',
					'traits_namespace'	=> 'EcampCore\ServiceTraits'
				),
			)
		),
			
		'acl' => array(
			'resources' => array(
				'EcampCore\Entity\Camp'		=>  null,
				'EcampCore\Entity\Period' 	=> 'EcampCore\Entity\Camp',
				'EcampCore\Entity\Day' 		=> 'EcampCore\Entity\Period',
				// ...
					
				'EcampCore\Entity\Group'	=> null,
				// ...
				
				'EcampCore\Entity\User'		=> null,
				// ...
			),
			
			'roles' => array(
				EcampCore\Entity\User::ROLE_GUEST	=> null,
				EcampCore\Entity\User::ROLE_USER	=> EcampCore\Entity\User::ROLE_GUEST,
				EcampCore\Entity\User::ROLE_ADMIN	=> EcampCore\Entity\User::ROLE_USER
			),
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
        	
        	'group' => array(
        		'type'    => 'Literal',
        		'options' => array(
        			'route'    => '/group',
        			'defaults' => array(
        				'__NAMESPACE__' => 'EcampCore\Controller',
        				'controller'    => 'Index',
        				'action'        => 'index',
        			),
        		),
        		'may_terminate' => true,
        		
        		'child_routes' => array(        		
		        	'camp' => array(
		        		'type' => 'EcampCore\Router\GroupCampRouter',
		        		'options' => array(
		        			'defaults' => array(),
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
		        			)	
		        		)
        			)
        		)
        	),
        	
        	'user' => array(
        		'type'    => 'Literal',
        		'options' => array(
        			'route'    => '/user',
        			'defaults' => array(
        				'__NAMESPACE__' => 'EcampCore\Controller',
        				'controller'    => 'Index',
        				'action'        => 'index',
        			),
        		),
        		'may_terminate' => true,
        		
        		'child_routes' => array(        		
		        	'camp' => array(
		        		'type' => 'EcampCore\Router\UserCampRouter',
		        		'options' => array(
		        			'defaults' => array(),
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
		        			)	
		        		)
        			)
        		)
        	)
		),
	),
    
    
	'view_manager' => array(
        'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
	
	'doctrine' => array(
		'driver' => array(
			'ecamp_core_entities' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/EcampCore/Entity')
			),
			
			'orm_default' => array(
				'drivers' => array(
					'EcampCore\Entity' => 'ecamp_core_entities'
				)
			)
		),
	),
	
);