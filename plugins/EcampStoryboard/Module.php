<?php
namespace EcampStoryboard;

use Zend\EventManager\SharedEventManagerInterface;
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
                    'EcampStoryboard' => __DIR__ . '/src/EcampStoryboard',
                    'EcampStoryboardTest' => __DIR__ . '/test/EcampStoryboard'
                ),
            ),
        );
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

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function renderCollectionResource($e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof \EcampStoryboard\Entity\Section) {
            $params['resource']    = new \EcampStoryboard\Resource\SectionResource($resource);

            return;
        }

    }

}
