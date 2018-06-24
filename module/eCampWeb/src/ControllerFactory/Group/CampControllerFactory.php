<?php

namespace eCamp\Web\ControllerFactory\Group;

use eCamp\Core\Service\CampService;
use eCamp\Web\Controller\Group\CampController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CampControllerFactory implements FactoryInterface {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var CampService $campService */
        $campService = $container->get(CampService::class);

        return new CampController($campService);
    }
}
