<?php

namespace eCamp\Lib\ServiceManager;

use eCamp\Lib\Acl\Acl;
use Interop\Container\ContainerInterface;
use ProxyManager\Proxy\LazyLoadingInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

class AclInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof LazyLoadingInterface && !$instance->isProxyInitialized()) {
            return;
        }

        if ($instance instanceof AclAware) { 
            /** @var Acl $acl */
            $acl = $container->get(Acl::class);
            $instance->setAcl($acl);
        }
    }
}
