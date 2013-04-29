<?php

namespace EcampCore\DI;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

abstract class DependencyLocator
	implements ServiceLocatorAwareInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	/**
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		while($serviceLocator instanceof AbstractPluginManager){
			$serviceLocator = $serviceLocator->getServiceLocator();
		}
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @return Zend\ServiceManager\ServiceLocatorInterface
	 */
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	
	public function __call($method, $args){
		if($this->serviceLocator->has('__repos__.' . $method)){
			return $this->serviceLocator->get('__repos__.' . $method);
		}
	
		if($this->serviceLocator->has('__services__.' . $method)){
			return $this->serviceLocator->get('__internal_services__.' . $method);
		}
	}
	
}