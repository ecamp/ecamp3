<?php

namespace eCamp\Lib\ServiceManager;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

class EntityFilterManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS =  EntityFilterManager::class;

    const CONFIG_KEY = 'entity_filter';
    const CONFIG_METHOD = 'getEntityFilterConfig';

    public static function initModule(ModuleManagerInterface $manager)
    {
        /** @var \Zend\ModuleManager\ModuleManager $manager */
        $sm = $manager->getEvent()->getParam('ServiceManager');
        /** @var \Zend\ModuleManager\Listener\ServiceListener $serviceListener */
        $serviceListener = $sm->get('ServiceListener');

        $serviceListener->addServiceManager(
            EntityFilterManager::class,
            self::CONFIG_KEY,
            EntityFilterProviderInterface::class,
            self::CONFIG_METHOD
        );
    }
}
