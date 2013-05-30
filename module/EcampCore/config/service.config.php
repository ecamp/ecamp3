<?php
return array(
	
	'aliases' => array(
	),
	
	'factories' => array(
		'ecampcore.acl' => 'EcampCore\Acl\AclFactory',
		
		'ecampcore.repo.camp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Camp'),
		'ecampcore.repo.camptype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\CampType'),
		'ecampcore.repo.day' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Day'),
		'ecampcore.repo.event' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Event'),
		'ecampcore.repo.eventcategory' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventCategory'),
		'ecampcore.repo.eventinstance' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventInstance'),
		'ecampcore.repo.eventprototype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventPrototype'),
		'ecampcore.repo.eventresp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventResp'),
		'ecampcore.repo.eventtemplate' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventTemplate'),
		'ecampcore.repo.eventtype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\EventType'),
		'ecampcore.repo.group' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Group'),
		'ecampcore.repo.grouprequest' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\GroupRequest'),
		'ecampcore.repo.image' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Image'),
		'ecampcore.repo.login' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Login'),
		'ecampcore.repo.medium' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Medium'),
		'ecampcore.repo.period' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Period'),
		'ecampcore.repo.plugin' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\Plugin'),
		'ecampcore.repo.plugininstance' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginInstance'),
		'ecampcore.repo.pluginposition' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginPosition'),
		'ecampcore.repo.pluginprototype' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\PluginPrototype'),
		'ecampcore.repo.uid' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UId'),
		'ecampcore.repo.user' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\User'),
		'ecampcore.repo.usercamp' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserCamp'),
		'ecampcore.repo.usergroup' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserGroup'),
		'ecampcore.repo.userrelationship' => new EcampLib\Repository\RepositoryFactory('EcampCore\Entity\UserRelationship'),
		
			
		'ecampcore.service.avatar' => function($sm){
			return new AvatarService(
				$sm->get('ecampcore.service.user'),
				$sm->get('ecampcore.servcie.group')
			);
		},
		
		'ecampcore.service.camp' => function($sm){
			return new EcampCore\Service\CampService(
				$sm->get('ecampcore.repo.camp'),
				$sm->get('ecampcore.service.period')
			);
		}
				
	),
	
	'invokables' => array(
		
	),
	
	'initializers' => array(
		function($instance, $sm){},
	),
);