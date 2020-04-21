<?php

namespace eCamp\Core\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class DefaultPluginServiceFactory extends BasePluginServiceFactory
    implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $eventPluginId = $this->getEventPluginId($container);
        
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get(AuthenticationService::class);

        /** @var AuthenticationService $authenticationService */
        $serviceUtils = $container->get(ServiceUtils::class);


        return new $requestedName($serviceUtils, $authenticationService, $eventPluginId);
    }
}