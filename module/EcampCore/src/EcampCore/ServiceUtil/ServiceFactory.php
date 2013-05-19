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
	private $serviceAlias;
	
	
	public function __construct($serviceAlias){
		$this->serviceAlias = $serviceAlias;
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$service = $serviceLocator->get($this->serviceAlias);
		$em = $serviceLocator->get('doctrine.entitymanager.orm_default');
		
		return new ServiceWrapper($em, $service);
	}
	
}