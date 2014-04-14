<?php

namespace EcampLib\Job;

use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractServiceJobBase extends AbstractJobBase
{
    /**
     * @var ServiceLocatorInterface
     */
    private static $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public static function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator()
    {
        return self::$serviceLocator;
    }

    protected function __construct($defaultQueue = 'service')
    {
        parent::__construct($defaultQueue);
    }
}
