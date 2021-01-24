<?php

namespace eCamp\Lib;

use Doctrine\DBAL\Types\Type;
use eCamp\Lib\Hal\CollectionRenderer;
use eCamp\Lib\ServiceManager\EntityFilterManagerFactory;
use eCamp\Lib\Types\Doctrine\DateTimeUtcType;
use eCamp\Lib\Types\Doctrine\DateUtcType;
use Laminas\EventManager\EventManager;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

class Module implements InitProviderInterface {
    public function getConfig(): array {
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

        Type::overrideType('date', DateUtcType::class);
        Type::overrideType('datetime', DateTimeUtcType::class);

        (new CollectionRenderer())->attach($events);
    }
}
