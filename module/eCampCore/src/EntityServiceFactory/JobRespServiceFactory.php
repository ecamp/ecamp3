<?php

namespace eCamp\Core\EntityServiceFactory;

use eCamp\Core\EntityService\JobRespService;
use eCamp\Core\ServiceManager\ServiceInjector;
use Interop\Container\ContainerInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

final class JobRespServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $proxyFactory = $container->get(LazyLoadingValueHolderFactory::class);

        $initializer = function(&$service, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer) use ($container) {
            $service = new JobRespService();
            ServiceInjector::Inject($container, $service);

            $initializer = null;
            return true;
        };
        $proxy = $proxyFactory->createProxy(JobRespService::class, $initializer);

        return $proxy;
    }
}
