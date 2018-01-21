<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\PeriodHydrator;
use eCamp\Core\Service\DayService;
use eCamp\Core\Service\PeriodService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class PeriodServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PeriodService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, PeriodHydrator::class);

        $dayService = $container->get(DayService::class);
        return new PeriodService($acl, $entityManager, $hydrator, $dayService);
    }
}
