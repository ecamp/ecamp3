<?php
return array(

    'aliases' => array(
    ),

    'factories' => array(
        'EcampLib\Acl'                          => 'EcampCore\Acl\AclFactory',
        'EcampCore\Plugin\StrategyProvider'     => 'EcampCore\Plugin\StrategyProviderFactory'
    ),

    'invokables' => array(
        'EcampLib\Service\ServiceInitializer'   => 'EcampCore\Service\Base\ServiceInitializer',
    ),

    'initializers' => array(
    ),
);
