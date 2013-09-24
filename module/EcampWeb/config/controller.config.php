<?php
return array(

    'invokables' => array(
        'EcampWeb\Controller\Index' 			=> 'EcampWeb\Controller\IndexController',
        'EcampWeb\Controller\Bypass' 			=> 'EcampWeb\Controller\BypassController',

        'EcampWeb\Controller\Group\Index' 		=> 'EcampWeb\Controller\Group\IndexController',
        'EcampWeb\Controller\Group\Members' 	=> 'EcampWeb\Controller\Group\MembersController',
        'EcampWeb\Controller\Group\Camps' 		=> 'EcampWeb\Controller\Group\CampsController',
    ),

    'factories' => array(

    )
);
