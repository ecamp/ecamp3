<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\DayHydrator;
use eCamp\Core\Service\DayService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;

class DayServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DayService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = $this->getHydrator($container, DayHydrator::class);

        return new DayService($hydrator);
    }
}
