<?php

namespace EcampLib\Log;

use Zend\Log\Writer\Stream;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $logger = new Logger();
        $logger->addWriter(new Stream(__DATA__ . '/log/core.log'));

        return $logger;
    }
}
