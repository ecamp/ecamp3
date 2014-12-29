<?php
use EcampStoryboard\StrategyFactory;

return array(
    'factories' => array(
        'EcampStoryboard\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        },

        'EcampStoryboard\Resource\SectionResourceListener' => function ($services) {
            $em = $services->get('doctrine.entitymanager.orm_default');
            $eventPluginRepo = $services->get('EcampCore\Repository\EventPlugin');
            $sectionRepo = $services->get('EcampStoryboard\Repository\Section');

            return new \EcampStoryboard\Resource\SectionResourceListener($em, $eventPluginRepo, $sectionRepo);
        },
    ),
);
