<?php

namespace EcampCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController
{
	
	/** @var EcampCore\ServiceUtil\ServiceProvider */
	private $serviceProvider;
	
	/** 
	 * @return EcampCore\ServiceUtil\ServiceProvider
	 */
	protected function service(){
		if($this->serviceProvider == null){
			$this->serviceProvider = $this->getServiceLocator()->get('ecamp.serviceutil.provider');
		}
		return $this->serviceProvider;
	}
}