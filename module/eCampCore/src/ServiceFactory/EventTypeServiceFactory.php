<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventTypeHydrator;
use eCamp\Core\Service\EventTypeService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventTypeServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventTypeService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, EventTypeHydrator::class);

        return new EventTypeService($hydrator);
    }
}
