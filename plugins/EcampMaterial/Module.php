<?php
namespace EcampMaterial;

use EcampMaterial\Listener\CollectionRenderingListener;

use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'EcampMaterial' => __DIR__ . '/src/EcampMaterial',
                    'EcampMaterialTest' => __DIR__ . '/test/EcampMaterialTest',
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $sharedEventManager = $event->getTarget()->getEventManager()->getSharedManager();

        (new CollectionRenderingListener())->attachShared($sharedEventManager);

    }

}
