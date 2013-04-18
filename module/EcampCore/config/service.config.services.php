<?php
return array(
	
	'factories' => array(
		'ecamp.service.avatar' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.avatar'),
		'ecamp.service.camp' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.camp'),
		'ecamp.service.contributon' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.contributon'),
		'ecamp.service.day' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.day'),
		'ecamp.service.eventinstance' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.eventinstance'),
		'ecamp.service.event' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.event'),
		'ecamp.service.group' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.group'),
		'ecamp.service.login' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.login'),
		'ecamp.service.membership' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.membership'),
		'ecamp.service.period' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.period'),
		'ecamp.service.register' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.register'),
		'ecamp.service.relationship' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.relationship'),
		'ecamp.service.searchuser' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.searchuser'),
		'ecamp.service.support' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.support'),
		'ecamp.service.user' => new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.user'),
	),

    'invokables' => array(
		'ecamp.internal.service.avatar' => 'EcampCore\Service\AvatarService',
		'ecamp.internal.service.camp' => 'EcampCore\Service\CampService',
		'ecamp.internal.service.contributon' => 'EcampCore\Service\ContributonService',
		'ecamp.internal.service.day' => 'EcampCore\Service\DayService',
		'ecamp.internal.service.eventinstance' => 'EcampCore\Service\EventInstanceService',
		'ecamp.internal.service.event' => 'EcampCore\Service\EventService',
		'ecamp.internal.service.group' => 'EcampCore\Service\GroupService',
		'ecamp.internal.service.login' => 'EcampCore\Service\LoginService',
		'ecamp.internal.service.membership' => 'EcampCore\Service\MembershipService',
		'ecamp.internal.service.period' => 'EcampCore\Service\PeriodService',
		'ecamp.internal.service.register' => 'EcampCore\Service\RegisterService',
		'ecamp.internal.service.relationship' => 'EcampCore\Service\RelationshipService',
		'ecamp.internal.service.searchuser' => 'EcampCore\Service\SearchUserService',
		'ecamp.internal.service.support' => 'EcampCore\Service\SupportService',
		'ecamp.internal.service.user' => 'EcampCore\Service\UserService',
    ),
    
);