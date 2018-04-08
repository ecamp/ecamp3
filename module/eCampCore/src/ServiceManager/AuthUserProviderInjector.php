<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Core\Auth\AuthUserProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class AuthUserProviderInjector implements InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param object $instance
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof AuthUserProviderAware) {
            $authUserProvider = $container->get(AuthUserProvider::class);
            $instance->setAuthUserProvider($authUserProvider);
        }
    }
}
