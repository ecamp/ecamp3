<?php
use EcampStoryboard\StrategyFactory;

return array(
    'factories' => array(
        'EcampStoryboard\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        },

        'EcampStoryboard\Resource\SectionResourceListener' => function ($services) {
            $repository = $services->get('EcampStoryboard\Repository\Section');

            return new \EcampStoryboard\Resource\SectionResourceListener($repository);
        },
    ),
);
