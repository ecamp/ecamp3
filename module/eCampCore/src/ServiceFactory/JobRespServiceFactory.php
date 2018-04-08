<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Service\JobRespService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class JobRespServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return JobRespService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, JobRespHydrator::class);

        return new JobRespService($hydrator);
    }
}
