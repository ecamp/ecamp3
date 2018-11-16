<?php

namespace eCamp\Lib\Auth\Storage;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthHeaderAndRedirectQueryParamFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $serviceLocator
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $headerStorage = new AuthHeaderAndRedirectQueryParam(
            $serviceLocator->get('Request'),
            $serviceLocator->get('Application')->getEventManager()
        );

        return $headerStorage;
    }

    /**
     * ZF2 backwards compatibility method
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return object
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, AuthHeaderAndRedirectQueryParam::class);
    }
}
