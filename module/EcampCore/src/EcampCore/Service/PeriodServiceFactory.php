<?php

namespace EcampCore\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Mvc\Service\ServiceListenerFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PeriodServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $periodResource = $container->get(\EcampApi\V1\Rest\Period\PeriodResource::class);
        $dayService = $container->get(DayService::class);

        return new PeriodService($periodResource, $dayService);
    }
}