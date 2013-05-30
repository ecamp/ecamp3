<?php

namespace EcampLib\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory 
	implements FactoryInterface
{
	private $serviceAlias;
	private $orm;
	
	public function __construct($serviceAlias, $orm = null){
		$this->serviceAlias = $serviceAlias;
		$this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$service = $serviceLocator->get($this->serviceAlias);
		$em = $serviceLocator->get($this->orm);
		
		return new ServiceWrapper($em, $service);
	}
	
}