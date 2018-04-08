<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Service\JobService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class JobServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return JobService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, JobHydrator::class);

        return new JobService($hydrator);
    }
}
