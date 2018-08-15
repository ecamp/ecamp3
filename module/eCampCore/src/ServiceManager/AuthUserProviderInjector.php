<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Core\Auth\AuthUserProvider;
use Interop\Container\ContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class AuthUserProviderInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        static::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof LazyLoadingInterface && !$instance->isProxyInitialized()) {
            return;
        }

        if ($instance instanceof AuthUserProviderAware) {
            $authUserProvider = $container->get(AuthUserProvider::class);
            $instance->setAuthUserProvider($authUserProvider);
        }
    }
}
