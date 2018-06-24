<?php

namespace eCamp\Plugin\Textarea\Service;

use eCamp\Core\Plugin\BasePluginServiceFactory;
use eCamp\Plugin\Textarea\Hydrator\TextareaHydrator;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TextareaServiceFactory extends BasePluginServiceFactory {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TextareaService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);
        $entityManager = $this->getEntityManager($container);
        $textareaHydrator = $this->getHydrator($container, TextareaHydrator::class);
        $eventPluginId = $this->getEventPluginId($container);

        return new TextareaService($acl, $entityManager, $textareaHydrator, $eventPluginId);
    }
}
