<?php

namespace eCamp\Lib;

use Doctrine\DBAL\Types\Type;
use eCamp\Lib\Hal\CollectionRenderer;
use eCamp\Lib\Listener\SentryErrorListener;
use eCamp\Lib\ServiceManager\EntityFilterManagerFactory;
use eCamp\Lib\Types\Doctrine\DateTimeUtcType;
use eCamp\Lib\Types\Doctrine\DateUtcType;
use Laminas\EventManager\EventManager;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

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

        // if sentry-configuration available, use sentry
        if (file_exists(__DIR__.'/../../../config/sentry.config.php')) {
            $this->registerSentry($app);
        }

        Type::overrideType('date', DateUtcType::class);
        Type::overrideType('datetime', DateTimeUtcType::class);

        (new CollectionRenderer())->attach($events);
    }

    public function registerSentry(Application $app) {
        $container = $app->getServiceManager();
        $eventManager = $app->getEventManager();

        /** @var SentryErrorListener $errorHandlerListener */
        $errorHandlerListener = $container->get(SentryErrorListener::class);
        $errorHandlerListener->attach($eventManager);
    }
}
