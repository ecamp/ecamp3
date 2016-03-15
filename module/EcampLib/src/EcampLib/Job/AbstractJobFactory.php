<?php

namespace EcampLib\Job;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractJobFactory
    implements JobFactoryInterface, ServiceLocatorAwareInterface
{
    /** @var \EcampLib\ServiceManager\JobFactoryManager */
    private $serviceLocator;

    /** @param ServiceLocatorInterface $serviceLocator */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /** @return \EcampLib\ServiceManager\JobFactoryManager */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getService($name)
    {
        return $this->serviceLocator->getServiceLocator()->get($name);
    }
}
