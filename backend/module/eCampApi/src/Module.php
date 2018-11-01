<?php

namespace eCamp\Api;

use eCamp\Api\View\AcaoInjector;
use Zend\Config\Factory;
use Zend\Mvc\MvcEvent;

class Module {
    public function getConfig() {
        return Factory::fromFiles(array_merge(
            [ __DIR__ . '/../config/module.config.php' ],
            glob(__DIR__ . '/../config/autoload/*.*'),
            glob(__DIR__ . '/../config/autoload/V1/*.*')
        ));
    }

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();
        $events = $app->getEventManager();

        $acaoInjector = new AcaoInjector();
        $acaoInjector->attach($events);
    }
}
