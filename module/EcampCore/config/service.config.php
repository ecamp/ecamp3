<?php
return array(
	
	'aliases' => array(
		'EcampCore\Acl\ContextStorage' => 'ecamp.acl.contextstorage'
	),
	
	'factories' => array(
		'ecamp.internal.acl' => function($sm){	return new EcampCore\Acl\DefaultAcl($sm);	},

		'ecamp.internal.serviceutil.provider' => 
			function($sm){	return new EcampCore\ServiceUtil\ServiceProvider($sm, true);	},

			
			
		'ecamp.serviceutil.provider' => 
			function($sm){	return new EcampCore\ServiceUtil\ServiceProvider($sm, false);	},
		
		'ecamp.repositoryutil.provider'	=> 
			function($sm){	return new EcampCore\RepositoryUtil\RepositoryProvider($sm);	},
		
			

		'ecamp.acl.contextprovider'	=> function($sm){
			$contextStorage = $sm->get('ecamp.acl.contextstorage');
			return new EcampCore\Acl\ContextProvider($contextStorage);
		},
		
		'ecamp.acl.contextstorage' => function($sm){
			$authService = new Zend\Authentication\AuthenticationService();
			$userRepo = $sm->get('ecamp.repo.user');
			$groupRepo = $sm->get('ecamp.repo.group');
			$campRepo = $sm->get('ecamp.repo.camp');
			return new EcampCore\Acl\ContextStorage($authService, $userRepo, $groupRepo, $campRepo);
		},
		
	),

);