<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Service\EventPluginService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventPluginServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventPluginService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, EventPluginHydrator::class);

        return new EventPluginService($acl, $entityManager, $hydrator, $container);
    }
}
