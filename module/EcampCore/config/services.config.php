<?php
return array(
	
	'aliases' => array(
		'EcampCore\Acl\ContextStorage' => 'ecamp.acl.contextstorage',
	),
	
	'factories' => array(
		'ecamp.serviceutil.provider' 	=> 
			function($sm){	return new EcampCore\ServiceUtil\ServiceProvider($sm, false);	},

		'ecamp.internal.serviceutil.provider' 	=> 
			function($sm){	return new EcampCore\ServiceUtil\ServiceProvider($sm, true);	},
		
		'ecamp.repositoryutil.provider'	=> 
			function($sm){	return new EcampCore\RepositoryUtil\RepositoryProvider($sm);	},
		
		
		'eacmp.service.avatar'		=> new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.avatar'),
		'eacmp.service.user'		=> new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.user'),
		'ecamp.service.login' 		=> new EcampCore\ServiceUtil\ServiceFactory('ecamp.internal.service.login'),
		
		'ecamp.repo.user' 			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\User'),
		'ecamp.repo.contributor'	=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\UserCamp'),
		'ecamp.repo.camp'			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Camp'),
		'ecamp.repo.period'			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Period'),
		'ecamp.repo.day'			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Day'),
		'ecamp.repo.event'			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Event'),
		'ecamp.repo.eventinstance'	=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventInstance'),
		'ecamp.repo.eventresp'		=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventResp'),
		'ecamp.repo.eventtemplate'	=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventTemplate'),
		'ecamp.repo.group'			=> new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Group'),
		
		
		'ecamp.acl.contextstorage'	=> function($sm){
			$authService = $sm->get('Zend\Authentication\AuthenticationService');
			$userRepo = $sm->get('ecamp.repo.user');
			$groupRepo = $sm->get('ecamp.repo.group');
			$campRepo = $sm->get('ecamp.repo.camp');
			return new EcampCore\Acl\ContextStorage($authService, $userRepo, $groupRepo, $campRepo);
		},
		
		'ecamp.acl.contextprovider'	=> function($sm){
			$contextStorage = $sm->get('ecamp.acl.contextstorage');
			return new EcampCore\Acl\ContextProvider($contextStorage);
		},
		
		'ecamp.acl.context' => function($sm){
			return $sm->get('ecamp.acl.contextprovider')->getContext();
		},
		
	),

    'invokables' => array(
    	'ecamp.internal.service.login' => 'EcampCore\Service\LoginService',
    ),
     
);