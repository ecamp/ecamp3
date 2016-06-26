<?php
return array(

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
                'ViewJsonStrategy',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_courseaim_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampCourseAim/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampCourseAim\Entity' => 'ecamp_courseaim_entities'
                )
            )
        ),

    ),

);
