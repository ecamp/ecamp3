<?php

namespace ApiApp\Serializer;


use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;
use CoreApi\Entity\Event;
use CoreApi\Entity\EventInstance;

class EventInstanceSerializer extends BaseSerializer{
	
	public function __invoke(EventInstance $eventInstance){
		$periodSerializer = new PeriodSerializer($this->mime);
		$eventSerializer = new EventSerializer($this->mime);
		
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
				'id' => $eventInstance->getId(),
				'mime' => $this->mime
			), 
			'api.v1.eventinstance'
		);
	}
	
	private function getPeriod_EventInstanceCollectionHref(Period $period){
		return
			$this->router->assemble(
				array(
					'period' => $period->getId(),
					'mime' => $this->mime
				),
			'api.v1.period.eventinstance.collection'
		);
	}
	
	private function getEvent_EventInstanceCollectionHref(Event $event){
		return
			$this->router->assemble(
				array(
					'event' => $event->getId(),
					'mime' => $this->mime
				),
				'api.v1.event.eventinstance.collection'
			);
	}
	
}