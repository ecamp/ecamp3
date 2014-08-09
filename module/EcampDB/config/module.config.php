<?php
return array(
    'router' => array(
        'routes' => array(
            'db' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/db',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EcampDB\Controller',
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
            'EcampDB\Controller\Index' 			=> 'EcampDB\Controller\IndexController',
            'EcampDB\Controller\Schema' 		=> 'EcampDB\Controller\SchemaController',
            'EcampDB\Controller\Maintenance' 	=> 'EcampDB\Controller\MaintenanceController',
            'EcampDB\Controller\Data' 			=> 'EcampDB\Controller\DataController',
            'EcampDB\Controller\Fixtures' 		=> 'EcampDB\Controller\FixturesController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'data-fixture' => array(
            'EcampDB_fixture' =>  __DIR__ . '/../src/EcampDB/Fixtures',
    )
);
