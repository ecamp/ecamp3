<?php

namespace EcampApi\Controller;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AssetsControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
		/** @var EntityManager $em */
		$em = $container->get('doctrine.entitymanager.orm_default');
		
		return new AssetsController($em);
	}
}
