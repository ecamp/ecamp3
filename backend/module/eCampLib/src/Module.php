<?php

namespace eCamp\Lib;

use eCamp\Lib\Hal\CollectionRenderer;
use eCamp\Lib\ServiceManager\EntityFilterManagerFactory;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class Module implements InitProviderInterface {
    public function getConfig() {
        return include __DIR__.'/../config/module.config.php';
    }

    public function init(ModuleManagerInterface $manager) {
        EntityFilterManagerFactory::initModule($manager);
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Application $app */
        $app = $e->getApplication();
        /** @var EventManager $events */
        $events = $app->getEventManager();

        (new CollectionRenderer())->attach($events);
    }
}
