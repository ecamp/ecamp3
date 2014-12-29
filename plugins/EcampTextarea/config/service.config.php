<?php
use EcampTextarea\StrategyFactory;

return array(
    'factories' => array(
        'EcampTextarea\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        },

        'EcampTextarea\Resource\TextareaResourceListener' => function ($services) {
            $em = $services->get('doctrine.entitymanager.orm_default');
            $eventPluginRepo = $services->get('EcampCore\Repository\EventPlugin');
            $textareaRepo = $services->get('EcampTextarea\Repository\Textarea');

            return new \EcampTextarea\Resource\TextareaResourceListener($em, $eventPluginRepo, $textareaRepo);
        },
    ),
);
