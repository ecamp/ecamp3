<?php
use EcampMaterial\Resource\MaterialItem\MaterialItemResourceListener;

use EcampMaterial\StrategyFactory;

return array(
    'factories' => array(
        'EcampMaterial\StrategyFactory' => function($serviceLocator){
            $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

            return new StrategyFactory($serviceLocator, $entityManager);
        },

        'EcampMaterial\Resource\MaterialItem\MaterialItemResourceListener' => function ($services) {
            $repository = $services->get('EcampMaterial\Repository\MaterialItem');

            return new MaterialItemResourceListener($repository);
        },

    ),
);
