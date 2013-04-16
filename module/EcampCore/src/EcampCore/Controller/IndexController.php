<?php

namespace EcampCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractBaseController 
{
	
	public function indexAction(){
		
		//die(print_r($loginService = $this->getServiceLocator()->getRegisteredServices()));
		
		
		$intUserService = $this->getServiceLocator()->get('ecamp.internal.service.login');
		
		die( get_class($intUserService->repo()->campRepository() ) );
		
		die( get_class($this->service()->userService() ) );
		
	}
	
}