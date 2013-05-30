<?php
return array(

	'factories' => array(
		
		'EcampCore\Controller\Test' => function($sm){    			
			return new EcampCore\Controller\TestController(
				$sm->getServiceLocator()->get('ecampcore.repo.user'),
				$sm->getServiceLocator()->get('ecampcore.repo.camp')
			);
		},
		
		'EcampCore\Controller\Index' => function($sm){
			return new EcampCore\Controller\IndexController(
				$sm->getServiceLocator()->get('ecampcore.repo.group'),
				$sm->getServiceLocator()->get('ecampcore.repo.camp'),
				$sm->getServiceLocator()->get('ecampcore.repo.user')
			);
		},
		
		'EcampCore\Controller\Login' => function($sm){
			return new EcampCore\Controller\LoginController(
				$sm->getServiceLocator()->get('ecampcore.repo.user')
			);
		},
		
		'EcampCore\Controller\Event' => function($sm){
			return new EcampCore\Controller\EventController(
				$sm->getServiceLocator()->get('ecampcore.repo.event')
			);
		}
		
	)
);