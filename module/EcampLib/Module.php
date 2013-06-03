<?php
namespace EcampLib;

use Zend\Mvc\MvcEvent;
use EcampLib\Entity\ServiceLocatorAwareEventListener;

class Module
{
    public function getConfig(){
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
    
    public function getServiceConfig(){
    	return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $e){
    	$sm = $e->getApplication()->getServiceManager();
    	 
    	$em = $sm->get('doctrine.entitymanager.orm_default');
    	$em->getEventManager()->addEventSubscriber(new ServiceLocatorAwareEventListener($sm));
    }
}
