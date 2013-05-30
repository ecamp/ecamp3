<?php

namespace EcampLib\Entity;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


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