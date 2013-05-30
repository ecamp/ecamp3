<?php

namespace EcampLib\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

class CommonControllerAbstractFactory
	implements AbstractFactoryInterface
{
	
	public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName){
		return class_exists($requestedName . 'Controller');
	}
	
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName){
		$controllerClass = $requestedName . 'Controller';
		return new $controllerClass();
	}
	
}
