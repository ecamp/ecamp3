<?php
namespace EcampApi\Hydrator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CampHydratorFactory implements FactoryInterface{
	
	public function __invoke(ContainerInterface $container, $requestedName, array $options = NULL) {
		
		return new CampHydrator($container->get('doctrine.entitymanager.orm_default'));
	}
}