<?php

namespace eCamp\Web\ControllerFactory\User;

use eCamp\Core\EntityService\CampService;
use eCamp\Web\Controller\User\CampController;
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
