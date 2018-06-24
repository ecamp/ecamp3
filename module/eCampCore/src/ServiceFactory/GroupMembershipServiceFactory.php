<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\GroupMembershipHydrator;
use eCamp\Core\Service\GroupMembershipService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class GroupMembershipServiceFactory extends BaseServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupMembershipService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, GroupMembershipHydrator::class);

        return new GroupMembershipService($hydrator);
    }
}
