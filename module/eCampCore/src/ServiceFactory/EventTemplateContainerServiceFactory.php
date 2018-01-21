<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventTemplateContainerHydrator;
use eCamp\Core\Service\EventTemplateContainerService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventTemplateContainerServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventTemplateContainerService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, EventTemplateContainerHydrator::class);

        return new EventTemplateContainerService($acl, $entityManager, $hydrator);
    }
}
