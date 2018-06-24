<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Core\Service\EventTypePluginService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventTypePluginServiceFactory extends BaseServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventTypePluginService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, EventTypePluginHydrator::class);

        return new EventTypePluginService($hydrator);
    }
}
