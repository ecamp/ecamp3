<?php
return array(

    'invokables' => array(
        'EcampWeb\Controller\Index' 				=> 'EcampWeb\Controller\IndexController',
        'EcampWeb\Controller\Bypass' 				=> 'EcampWeb\Controller\BypassController',

        'EcampWeb\Controller\Group\Index' 			=> 'EcampWeb\Controller\Group\IndexController',
        'EcampWeb\Controller\Group\Members' 		=> 'EcampWeb\Controller\Group\MembersController',
        'EcampWeb\Controller\Group\Camps' 			=> 'EcampWeb\Controller\Group\CampsController',

        'EcampWeb\Controller\Camp\Index'  			=> 'EcampWeb\Controller\Camp\IndexController',
        'EcampWeb\Controller\Camp\Collaborations'	=> 'EcampWeb\Controller\Camp\CollaborationsController'
    ),

    'factories' => array(

    )
);
