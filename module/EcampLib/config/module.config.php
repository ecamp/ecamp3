<?php
return array(
    'controllers' => array(
        'abstract_factories' => array(
//			'EcampLib\Controller\CommonControllerAbstractFactory'
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_lib_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampLib/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampLib\Entity' => 'ecamp_lib_entities'
                )
            )
        ),
    ),
);
