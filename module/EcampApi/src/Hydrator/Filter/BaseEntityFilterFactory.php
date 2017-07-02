<?php

namespace EcampApi\Hydrator\Filter;

use Zend\ServiceManager\Factory\FactoryInterface;
use DoctrineModule\Stdlib\Hydrator\Filter\PropertyName;
use Interop\Container\ContainerInterface;

class BaseEntityFilterFactory implements FactoryInterface {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
		return new PropertyName(['createdAt', 'updatedAt']);
	}
}
