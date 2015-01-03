<?php
use EcampMaterial\Resource\MaterialList\MaterialListResourceListener;

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
            $listRepository = $services->get('EcampMaterial\Repository\MaterialList');

            return new MaterialItemResourceListener($repository, $listRepository);
        },

        'EcampMaterial\Resource\MaterialList\MaterialListResourceListener' => function ($services) {
            $repository = $services->get('EcampMaterial\Repository\MaterialList');

            $eventPluginRepository = $services->get('EcampCore\Repository\EventPlugin');

            return new MaterialListResourceListener($repository, $eventPluginRepository);
        },

    ),
);
