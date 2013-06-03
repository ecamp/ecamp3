<?php

namespace EcampWeb\Controller;

use EcampLib\Controller\AbstractBaseController;
use EcampWeb\Form\User\BypassLoginForm;

class BypassController 
	extends AbstractBaseController
{
	
	public function indexAction(){
		
		$orm = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$login = new BypassLoginForm($orm);
		$login->setAttribute('action', $this->url()->fromRoute(
		    'web/default',
		    array(
		        'controller' => 'bypass',
		        'action'     => 'index',
		    )
		));
		
		return array(
			'login' => $login,
			'login_data' => $this->params()->fromQuery()
		);
		
	}
	
}