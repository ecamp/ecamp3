<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Core\Service\EventPluginService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Permissions\Acl\AclInterface;

class EventPluginServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EventPluginService|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, EventPluginHydrator::class);

        /** @var PluginStrategyProvider $pluginStrategyProvider */
        $pluginStrategyProvider = $container->get(PluginStrategyProvider::class );

        return new EventPluginService($acl, $entityManager, $hydrator, $pluginStrategyProvider);
    }
}
