<?php

namespace EcampLib\Resource;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractResourceFactory implements AbstractFactoryInterface
{
    private $pattern = "/^Ecamp(\w+)\\\\Resource\\\\([\w|\\\\]+)$/";

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return
            preg_match($this->pattern, $requestedName) &&
            class_exists($requestedName) &&
            is_subclass_of($requestedName, 'EcampLib\Resource\BaseResourceListener');
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return new $requestedName($serviceLocator);
    }
}
