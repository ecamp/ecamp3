<?php
return array(

    'invokables' => array(
        'EcampWeb\Controller\Index' 				    => 'EcampWeb\Controller\IndexController',
        'EcampWeb\Controller\Camp'                      => 'EcampWeb\Controller\CampController',
        'EcampWeb\Controller\Bypass' 				    => 'EcampWeb\Controller\BypassController',
        'EcampWeb\Controller\EventPlugin' 			    => 'EcampWeb\Controller\EventPluginController',

        'EcampWeb\Controller\Auth\Login'			    => 'EcampWeb\Controller\Auth\LoginController',
        'EcampWeb\Controller\Auth\Register'			    => 'EcampWeb\Controller\Auth\RegisterController',

        'EcampWeb\Controller\User\Index'                => 'EcampWeb\Controller\User\IndexController',

        'EcampWeb\Controller\Group\Index' 			    => 'EcampWeb\Controller\Group\IndexController',
        'EcampWeb\Controller\Group\Members' 		    => 'EcampWeb\Controller\Group\MembersController',
        'EcampWeb\Controller\Group\Camps' 			    => 'EcampWeb\Controller\Group\CampsController',
        'EcampWeb\Controller\Group\MembershipHelper' 	=> 'EcampWeb\Controller\Group\MembershipHelperController',

        'EcampWeb\Controller\Camp\Index'  			    => 'EcampWeb\Controller\Camp\IndexController',
        'EcampWeb\Controller\Camp\Period'  			    => 'EcampWeb\Controller\Camp\PeriodController',
        'EcampWeb\Controller\Camp\Day'  			    => 'EcampWeb\Controller\Camp\DayController',
        'EcampWeb\Controller\Camp\Event'  			    => 'EcampWeb\Controller\Camp\EventController',
        'EcampWeb\Controller\Camp\Job'  			    => 'EcampWeb\Controller\Camp\JobController',
        'EcampWeb\Controller\Camp\Picasso'  		    => 'EcampWeb\Controller\Camp\PicassoController',
        'EcampWeb\Controller\Camp\Collaborations'	    => 'EcampWeb\Controller\Camp\CollaborationsController',
        'EcampWeb\Controller\Camp\CollaborationHelper' 	=> 'EcampWeb\Controller\Camp\CollaborationHelperController',

    ),

    'factories' => array(

    )
);
