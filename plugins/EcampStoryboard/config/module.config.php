<?php
return array(
	'ecamp' => array(
		'modules' => array(
			'storyboard' => array(
				'repos' => array(
					'module_namespace' => 'EcampStoryboard',
					'config_file' => __DIR__ . '/service.config.repos.php',
				),
					
				'services' => array(
					'services_path' => __DIR__ . '/../src/EcampStoryboard/Service/',
					'config_file' => __DIR__ . '/service.config.services.php',
				)
			)
		)
	),
		
    'router' => array(
        'routes' => array(
            'plugin-storyboard' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/plugin/storyboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EcampStoryboard\Controller',
                        'controller'    => 'Index',
                    ),
                ),
 
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
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
				),
            ),
		),
	),
	
    'controllers' => array(
        'invokables' => array(
            'EcampStoryboard\Controller\Section' => 'EcampStoryboard\Controller\SectionController',
        ),
    ),

	'view_manager' => array(
        'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
	
	
	'doctrine' => array(
		'driver' => array(
			'ecamp_storyboard_entities' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/EcampStoryboard/Entity')
			),
	
			'orm_default' => array(
				'drivers' => array(
					'EcampStoryboard\Entity' => 'ecamp_storyboard_entities'
				)
			)
		),
		
		'configuration' => array(
			'orm_default' => array(
				'filters' => array(
					'storyboard-section' => 'EcampStoryboard\DbFilter\SectionFilter'
				)
			)
		)
	),
	
);