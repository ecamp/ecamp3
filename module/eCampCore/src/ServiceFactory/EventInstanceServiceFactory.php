<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Service\EventInstanceService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventInstanceServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventInstanceService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, EventInstanceHydrator::class);

        return new EventInstanceService($hydrator);
    }
}
