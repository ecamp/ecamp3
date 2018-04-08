<?php

namespace eCamp\Web\ControllerFactory;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Service\CampService;
use eCamp\Web\Controller\CampsController;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CampsControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampsController|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var AuthService $authService */
        $authService = $container->get(AuthService::class);
        /** @var CampService $campService */
        $campService = $container->get(CampService::class);

        return new CampsController($authService, $campService);
    }
}
