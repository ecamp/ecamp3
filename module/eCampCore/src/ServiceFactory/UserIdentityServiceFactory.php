<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Service\UserIdentityService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UserIdentityServiceFactory implements FactoryInterface
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
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        return new UserIdentityService($acl, $entityManager);
    }
}
