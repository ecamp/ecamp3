<?php

namespace EcampCore\RepositoryUtil;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryFactory implements FactoryInterface
{
	private $entityName;
	
	public function __construct($entityName){
		$this->entityName = $entityName;
	}
	
	public function createService(ServiceLocatorInterface $serviceLocator){
		$em = $serviceLocator->get('doctrine.entitymanager.orm_default');
		return $em->getRepository($this->entityName);
	}
	
}