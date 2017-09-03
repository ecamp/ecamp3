<?php
namespace EcampApi\V1;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DoctrineHydratorFactory implements FactoryInterface {
	
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
		/** @var EntityManager $entityManager */
		$entityManager = $container->get('doctrine.entitymanager.orm_default');
		
		return new $requestedName($entityManager);
	}
	
}
