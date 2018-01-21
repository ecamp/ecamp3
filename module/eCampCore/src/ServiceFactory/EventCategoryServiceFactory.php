<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventCategoryHydrator;
use eCamp\Core\Service\EventCategoryService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventCategoryServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventCategoryService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, EventCategoryHydrator::class);

        return new EventCategoryService($acl, $entityManager, $hydrator);
    }
}
