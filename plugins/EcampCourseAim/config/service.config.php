<?php

use EcampCourseAim\StrategyFactory;

use EcampCourseAim\Resource\Item\ItemResourceListener;

return array(
    'factories' => array(
        'EcampCourseAim\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        },

        'EcampCourseAim\Resource\Item\ItemResourceListener' => function ($services) {
            $repository = $services->get('EcampCourseAim\Repository\Item');
            $eventPluginRepository = $services->get('EcampCore\Repository\EventPlugin');
            $em = $services->get('doctrine.entitymanager.orm_default');

            return new ItemResourceListener($repository, $eventPluginRepository, $em);
        },


));
