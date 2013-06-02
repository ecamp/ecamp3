<?php

namespace EcampLib\Repository;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * Provides repositories for all doctrine entities
 * Pattern: EcampCore\Repository\*
 */
class AbstractRepositoryFactory implements AbstractFactoryInterface
{
	private $orm;
	
	public function __construct($orm = null){
		$this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
	}
	
	/**
	 * Translate a repository class name into the corresponding entity class name
	 * @param string $repoName
	 * @return string
	 */
	private function getEntityClassName($repoName)
	{
		$parts       = explode('\\', $repoName);
		$entityName  = $parts[2];
		$entityClass = 'EcampCore\\Entity\\' . $entityName ;
		return $entityClass;
	}
	
	public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		return preg_match("/^EcampCore\\\\Repository\\\\[a-zA-Z]+$/",$requestedName) && class_exists($this->getEntityClassName($requestedName));
	}
	
	public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
	{
		/* Reository is fetched from Doctrine EntityManager */
		$em = $serviceLocator->get($this->orm);
		return $em->getRepository($this->getEntityClassName($requestedName));
	}
}