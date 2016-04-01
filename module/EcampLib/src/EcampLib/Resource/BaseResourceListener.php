<?php

namespace EcampLib\Resource;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class BaseResourceListener extends AbstractListenerAggregate
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    protected function getService($name)
    {
        return $this->serviceLocator->get($name);
    }
}
