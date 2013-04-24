<?php

namespace EcampCore\EntityUtil;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Zend\ServiceManager\ServiceLocatorInterface;

use EcampCore\Entity\PluginInstance;


class ServiceLocatorAwareEventListener 
	implements EventSubscriber
{
    
	private $serviceLocator;
	
	public function __construct(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	public function postLoad(LifecycleEventArgs $event){
		$entity = $event->getEntity();
		
		if($entity instanceof  ServiceLocatorAwareInterface){
			$entity->setServiceLocator($this->serviceLocator);
		}
	}

    public function getSubscribedEvents(){
        return array(Events::postLoad);
    }
    
}