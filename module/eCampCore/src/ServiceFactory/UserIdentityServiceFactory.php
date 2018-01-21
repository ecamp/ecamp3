<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\UserIdentityHydrator;
use eCamp\Core\Service\UserIdentityService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class UserIdentityServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return UserIdentityService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, UserIdentityHydrator::class);

        return new UserIdentityService($acl, $entityManager, $hydrator);
    }
}
