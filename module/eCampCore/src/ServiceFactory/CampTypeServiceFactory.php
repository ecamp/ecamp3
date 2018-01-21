<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Hydrator\CampTypeHydrator;
use eCamp\Core\Service\CampTypeService;
use eCamp\Lib\Service\BaseServiceFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CampTypeServiceFactory extends BaseServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampTypeService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);

        $entityManager = $this->getEntityManager($container);
        $hydrator = $this->getHydrator($container, CampTypeHydrator::class);

        return new CampTypeService($acl, $entityManager, $hydrator);
    }
}
