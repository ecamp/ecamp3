<?php

namespace EcampCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractBaseController 
{
	
	public function indexAction(){
		
		//die(print_r($loginService = $this->getServiceLocator()->getRegisteredServices()));
		
		
		die( get_class($this->service()->loginService()->service() ) );
		
		die( get_class($this->service()->userService() ) );
		
	}
	
}