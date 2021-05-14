<?php

namespace eCamp\Lib\ServiceManager;

use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class EntityFilterManagerFactory extends AbstractPluginManagerFactory {
    public const PLUGIN_MANAGER_CLASS = EntityFilterManager::class;

    public const CONFIG_KEY = 'entity_filter';
    public const CONFIG_METHOD = 'getEntityFilterConfig';

    public static function initModule(ModuleManagerInterface $manager): void {
        /** @var \Laminas\ModuleManager\ModuleManager $manager */
        $sm = $manager->getEvent()->getParam('ServiceManager');
        /** @var \Laminas\ModuleManager\Listener\ServiceListener $serviceListener */
        $serviceListener = $sm->get('ServiceListener');

        $serviceListener->addServiceManager(
            EntityFilterManager::class,
            self::CONFIG_KEY,
            EntityFilterProviderInterface::class,
            self::CONFIG_METHOD
        );
    }
}
