<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventTypeFactoryHydrator;
use eCamp\Core\Service\EventTypeFactoryService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventTypeFactoryServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventTypeFactoryService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, EventTypeFactoryHydrator::class);

        return new EventTypeFactoryService($hydrator);
    }
}
