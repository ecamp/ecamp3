<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Service\CampTypeService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class CampTypeServiceFactory extends BaseServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampTypeService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, CampTypeHydrator::class);

        return new CampTypeService($hydrator);
    }
}
