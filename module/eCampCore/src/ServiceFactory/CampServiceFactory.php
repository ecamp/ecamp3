<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\CampHydrator;
use eCamp\Core\Service\CampService;
use eCamp\Core\Service\EventCategoryService;
use eCamp\Core\Service\JobService;
use eCamp\Core\Service\PeriodService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class CampServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, CampHydrator::class);

        $jobService = $container->get(JobService::class);
        $eventCategoryService = $container->get(EventCategoryService::class);
        $periodService = $container->get(PeriodService::class);

        return new CampService(
            $hydrator,
            $jobService,
            $eventCategoryService,
            $periodService
        );
    }
}
