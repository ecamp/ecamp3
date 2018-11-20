<?php

namespace eCamp\Api;

use eCamp\Lib\Auth\MvcAuth\JwtAdapter;
use Zend\Config\Factory;
use Zend\Mvc\MvcEvent;

class Module {
    public function getConfig() {
        return Factory::fromFiles(array_merge(
            [__DIR__ . '/../config/module.config.php'],
            glob(__DIR__ . '/../config/autoload/*.*'),
            glob(__DIR__ . '/../config/autoload/V1/*.*')
        ));
    }

    public function onBootstrap(MvcEvent $event) {
        $app      = $event->getApplication();
        $events   = $app->getEventManager();
        $services = $app->getServiceManager();

        $events->attach(
            'authentication',
            function ($e) use ($services) {
                $listener = $services->get('ZF\MvcAuth\Authentication\DefaultAuthenticationListener');
                $adapter = $services->get(JwtAdapter::class);
                $listener->attach($adapter);
            },
            1000
        );
    }
}
