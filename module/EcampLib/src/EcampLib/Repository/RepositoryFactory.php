<?php

namespace EcampLib\Repository;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryFactory implements FactoryInterface
{
	private $entityName;
	private $orm;
	
	public function __construct($entityName, $orm = null){
		$this->entityName = $entityName;
		$this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$em = $serviceLocator->get($this->orm);
		return $em->getRepository($this->entityName);
	}
	
}