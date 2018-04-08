<?php

namespace eCamp\Lib\ServiceManager;


use eCamp\Lib\Acl\Acl;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class AclInjector implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $instance) {
        if($instance instanceof AclAware) {
            /** @var Acl $acl */
            $acl = $container->get(Acl::class);
            $instance->setAcl($acl);
        }
    }
}
