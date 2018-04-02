<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\UserHydrator;
use eCamp\Core\Service\UserService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class UserServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return UserService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, UserHydrator::class);

        return new UserService($hydrator);
    }
}
