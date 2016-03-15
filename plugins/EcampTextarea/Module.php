<?php

namespace EcampTextarea;

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
                    'EcampTextarea' => __DIR__ . '/src/EcampTextarea',
                    'EcampTextareaTest' => __DIR__ . '/test/EcampTextareaTest'
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
        /** @var SharedEventManagerInterface $sharedEventManager */
        $sharedEventManager = $event->getTarget()->getEventManager()->getSharedManager();

        $sharedEventManager->attach(
            'PhlyRestfully\Plugin\HalLinks',
            'renderCollection.resource',
            array($this, 'renderCollectionResource'),
            100
        );
    }

    public function renderCollectionResource($e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof \EcampTextarea\Entity\Textarea) {
            $params['resource']    = new \EcampTextarea\Resource\TextareaResource($resource);

            return;
        }

    }

}
