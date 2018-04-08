<?php

namespace eCamp\Lib\ServiceManager;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class EntityManagerInjector implements InitializerInterface
{

    /**
     * @param ContainerInterface $container
     * @param object $instance
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof EntityManagerAware) {
            /** @var EntityManager $entityManager */
            $entityManager = $container->get('doctrine.entitymanager.orm_default');
            $instance->setEntityManager($entityManager);
        }
    }
}
