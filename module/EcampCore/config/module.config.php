<?php
return array(
    'router' => array(
        'routes' => array(
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
            'EcampCore\Controller\Login'	=> 'EcampCore\Controller\LoginController'
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
		)
	)
);