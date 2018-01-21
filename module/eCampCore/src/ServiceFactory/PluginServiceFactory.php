<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Service\PluginService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class PluginServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PluginService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, PluginHydrator::class);

        return new PluginService($acl, $entityManager, $hydrator);
    }
}
