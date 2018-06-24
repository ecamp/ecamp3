<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Service\EventTemplateService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class EventTemplateServiceFactory extends BaseServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventTemplateService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, EventTemplateHydrator::class);

        return new EventTemplateService($hydrator);
    }
}
