<?php
return array(
    'router' => array(
        'routes' => array(
            'api' => include __DIR__ . '/routes/api.v0.php'
        ),
    ),

    'phlyrestfully' => array(
        'resources' => include __DIR__ . '/phlyrestfully/resources.config.php'
    ),

    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_api_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampApi/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampApi\Entity' => 'ecamp_api_entities'
                )
            )
        ),
    ),

);
