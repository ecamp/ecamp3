<?php

namespace eCamp\Lib\ServiceManager;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class EntityManagerInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof LazyLoadingInterface && !$instance->isProxyInitialized()) {
            return;
        }

        if ($instance instanceof EntityManagerAware) {
            /** @var EntityManager $entityManager */
            $entityManager = $container->get('doctrine.entitymanager.orm_default');
            $instance->setEntityManager($entityManager);
        }
    }
}
