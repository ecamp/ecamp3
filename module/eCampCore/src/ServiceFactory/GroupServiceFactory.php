<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\GroupHydrator;
use eCamp\Core\Service\GroupService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class GroupServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, GroupHydrator::class);

        return new GroupService($hydrator);
    }
}
