<?php

namespace EcampApi\Serializer;


use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;

class EventInstanceSerializer extends BaseSerializer{
	
	public function serialize($eventInstance){
		$periodSerializer = new PeriodSerializer($this->format, $this->router);
		$eventSerializer = new EventSerializer($this->format, $this->router);
		
		return array(
    		'id' 		=> 	$eventInstance->getId(),
			'href'		=>	$this->getEventInstanceHref($eventInstance),
			'start'		=> 	$eventInstance->getStartTime()->getTimestamp(),
			'end'		=> 	$eventInstance->getEndTime()->getTimestamp(),
			'event'		=> 	$eventSerializer->getReference($eventInstance->getEvent()),
			'period'	=> 	$periodSerializer->getReference($eventInstance->getPeriod())
		);
	}
	
	public function getReference(EventInstance $eventInstance = null){
		if($eventInstance == null){
			return null;
		} else {
			return array(
				'id'	=>	$eventInstance->getId(),
				'href'	=>	$this->getEventInstanceHref($event)
			);
		}
	}
	
	public function getCollectionReference($collectionOwner){
		
		if($collectionOwner instanceof Event){
			return array('href' => $this->getEvent_EventInstanceCollectionHref($collectionOwner));
		}
		
		if($collectionOwner instanceof Period){
			return array('href' => $this->getPeriod_EventInstanceCollectionHref($collectionOwner));
		}
		
		return null;
	}
	
	private function getEventInstanceHref(EventInstance $eventInstance){
		return
			$this->router->assemble(
				array(
					'controller' => 'eventinstance',
					'action' => 'getList',
					'id' => $eventInstance->getId(),
					'format' => $this->format
				), 
				array(
					'name' => 'api/default'
				)
		);
	}
	
	private function getPeriod_EventInstanceCollectionHref(Period $period){
		return
			$this->router->assemble(
				array(
					'controller' => 'period',
					'action' => 'getList',
					'period' => $period->getId(),
					'format' => $this->format
				),
				array(
					'name' => 'api/default'
				)
		);
	}
	
	private function getEvent_EventInstanceCollectionHref(Event $event){
		return
			$this->router->assemble(
				array(
					'controller' => 'event',
					'action' => 'getList',
					'event' => $event->getId(),
					'format' => $this->format
				),
				array(
					'name' => 'api/default'
				)
			);
	}
	
}