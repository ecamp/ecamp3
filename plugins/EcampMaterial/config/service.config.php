<?php
use EcampMaterial\StrategyFactory;

return array(
    'factories' => array(
        'EcampMaterial\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        }
    ),
);
