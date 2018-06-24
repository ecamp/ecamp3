<?php

namespace eCamp\Plugin\Storyboard\Service;

use eCamp\Core\Plugin\BasePluginServiceFactory;
use eCamp\Plugin\Storyboard\Hydrator\SectionHydrator;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SectionServiceFactory extends BasePluginServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SectionService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);
        $entityManager = $this->getEntityManager($container);
        $textareaHydrator = $this->getHydrator($container, SectionHydrator::class);
        $eventPluginId = $this->getEventPluginId($container);

        return new SectionService($acl, $entityManager, $textareaHydrator, $eventPluginId);
    }
}
