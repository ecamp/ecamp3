<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Service\OrganizationService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class OrganizationServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return OrganizationService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, OrganizationHydrator::class);

        return new OrganizationService($acl, $entityManager, $hydrator);
    }
}
