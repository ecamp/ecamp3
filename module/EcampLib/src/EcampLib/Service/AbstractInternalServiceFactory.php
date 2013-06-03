<?php

namespace EcampLib\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use EcampLib\Service\ServiceBaseFactory;
use Zend\Authentication\AuthenticationService;

/**
 * Provides internal services for all existing service classes 
 * Pattern: Ecamp*\Service\*\Internal
 */
class AbstractInternalServiceFactory implements AbstractFactoryInterface
{
	private $orm;
	
	private $pattern = "/^Ecamp(\w+)\\\\Service\\\\(\w+)\\\\Internal$/";
	
	public function __construct($orm = null){
		$this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
	}
	
	private function getServiceFactoryName($serviceName)
	{
		return preg_replace('/\\\\Internal$/','ServiceFactory',$serviceName);
	}
	
	public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return preg_match($this->pattern,$requestedName) && class_exists($this->getServiceFactoryName($requestedName));
	}
	
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		/* Create service with specific service factory which does the wiring */
		/* e.g. Ecamp*\Service\***ServiceFactory */
		$serviceFactoryName = $this->getServiceFactoryName($requestedName);
		$serviceFactory = new $serviceFactoryName;
		$service = $serviceFactory->createService($serviceLocator);
		
		/* Inject common dependencies (e.g. dependencies of ServiceBase class) */
		$service->setEntityManager($serviceLocator->get($this->orm));
		$service->setAcl($serviceLocator->get('EcampCore\Acl'));
		
		$authId = (new AuthenticationService())->getIdentity();
		$service->setMe($serviceLocator->get('EcampCore\Repository\User')->find($authId));
		
		return $service;
	}
}