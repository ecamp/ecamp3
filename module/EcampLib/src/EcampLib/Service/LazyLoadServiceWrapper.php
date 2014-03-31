<?php

namespace EcampLib\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class LazyLoadServiceWrapper
{

    private $serviceLocator;
    private $serviceFactoryName;
    private $initService;
    private $service = null;

    public function __construct(ServiceLocatorInterface $serviceLocator, $serviceFactoryName, $initService)
    {
        $this->serviceLocator = $serviceLocator;
        $this->serviceFactoryName = $serviceFactoryName;
        $this->initService = $initService;
    }

    private function getService()
    {
        if ($this->service == null) {
            $serviceFactoryName = $this->serviceFactoryName;
            $serviceFactory = new $serviceFactoryName();
            $this->service = $serviceFactory->createService($this->serviceLocator);
            $initService = $this->initService;
            $initService($this->serviceLocator, $this->service);

            $this->serviceLocator = null;
            $this->serviceFactoryName = null;
            $this->initService = null;
        }

        return $this->service;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->getService(), $method), $args);
    }

    public function __get($name)
    {
        return $this->getService()[$name];
    }

    public function __set($name, $value)
    {
        $this->getService()[$name] = $value;
    }
}
