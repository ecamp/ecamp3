<?php
use EcampStoryboard\StrategyFactory;

return array(
    'factories' => array(
        'EcampStoryboard\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        }
    ),
);
