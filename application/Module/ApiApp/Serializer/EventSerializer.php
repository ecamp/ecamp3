<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\Camp;
use CoreApi\Entity\Event;

class EventSerializer extends BaseSerializer{
	
	public function __invoke(Event $event){
		$campSerializer = new CampSerializer($this->mime);
		$eventInstanceSerializer = new EventInstanceSerializer($this->mime);
		
		return array(
    		'id' 				=> 	$event->getId(),
			'href'				=>	$this->getEventHref($event),
			'title'				=> 	$event->getTitle(),
			'camp'				=> 	$campSerializer->getReference($event->getCamp()),
			'eventInstances'	=> 	$eventInstanceSerializer->getCollectionReference($event),
		);
	}
	
	public function getReference(Event $event = null){
		if($event == null){
			return null;
		} else {
			return array(
				'id'	=>	$event->getId(),
				'href'	=>	$this->getEventHref($event)
			);
		}
	}
	
	public function getCollectionReference($collectionOwner){
		
		if($collectionOwner instanceof Camp){
			return array('href' => $this->getCamp_EventCollectionHref($collectionOwner));
		}
		
		return null;
	}
	
	private function getEventHref(Event $event){
		return
			$this->router->assemble(
			array(
				'id' => $event->getId(),
				'mime' => $this->mime
			), 
			'api.v1.event'
		);
	}
	
	private function getCamp_EventCollectionHref(Camp $camp){
		return
			$this->router->assemble(
				array(
					'camp' => $camp->getId(),
					'mime' => $this->mime
				),
				'api.v1.camp.event.collection'
			);
	}
	
}