<?php

namespace EcampCore\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

abstract class AbstractRestfulBaseController extends AbstractRestfulController
{
	
	public function __call($method, $args){
		if($this->serviceLocator->has('__repos__.' . $method)){
			return $this->serviceLocator->get('__repos__.' . $method);
		}
	
		if($this->serviceLocator->has('__services__.' . $method)){
			return $this->serviceLocator->get('__services__.' . $method);
		}
	
		return parent::__call($method, $args);
	}
	
}