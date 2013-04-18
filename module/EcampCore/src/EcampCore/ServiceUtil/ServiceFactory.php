<?php

namespace EcampCore\ServiceUtil;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory 
	implements FactoryInterface
{
	
	/**
	 * @var string
	 */
	private $service;
	
	
	public function __construct($service){
		$this->service = $service;
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$service = $serviceLocator->get($this->service);
		$serviceWrapper = new ServiceWrapper($serviceLocator, $service);
		
		return $serviceWrapper;
	}
	
}