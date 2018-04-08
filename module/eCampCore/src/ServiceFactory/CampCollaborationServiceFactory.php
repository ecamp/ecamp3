<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Core\Service\CampCollaborationService;
use eCamp\Core\Service\CampService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class CampCollaborationServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $hydrator = $this->getHydrator($container, CampCollaborationHydrator::class);

        return new CampCollaborationService($hydrator);
    }
}
