<?php
namespace EcampLib;

use Zend\Mvc\MvcEvent;
use EcampLib\Entity\ServiceLocatorAwareEventListener;

use EcampLib\Listener\FlushEntitiesListener;

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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function getViewHelperConfig()
    {
        return include __DIR__ . '/config/view.helper.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $em = $sm->get('doctrine.entitymanager.orm_default');
        $em->getEventManager()->addEventSubscriber(new ServiceLocatorAwareEventListener($sm));

        /* listener for flushing entity manager */
        $eventManager = $e->getTarget()->getEventManager();
        $eventManager->attach(new FlushEntitiesListener());

        \Resque::setBackend('localhost:6379', 0, 'ecamp3');
    }
}

require_once __DIR__ . '/src/' . __NAMESPACE__ . '/Util/password.php';
