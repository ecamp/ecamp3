<?php

use EcampCourseAim\StrategyFactory;

return array(
    'factories' => array(
        'EcampCourseAim\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        }
));
