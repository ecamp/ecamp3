<?php
return array(

    'aliases' => array(
    ),

    'factories' => array(
        'EcampCore\Acl' => 'EcampCore\Acl\AclFactory',
    ),

    'invokables' => array(
        'EcampCore\Service\Period\Internal' => 'EcampCore\Service\PeriodService',
    ),

    'initializers' => array(
    ),
);
