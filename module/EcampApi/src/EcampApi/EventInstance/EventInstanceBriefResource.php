<?php
namespace EcampApi\EventInstance;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\EventInstance as EventInstance;
use EcampApi\Event\EventBriefResource;


class EventInstanceBriefResource extends HalResource{
	
	public function __construct(EventInstance $entity){
		
		$object = array(
				'id'		=> 	$entity->getId(),
	            'start'		=> 	$entity->getStartTime()->format(\DateTime::ISO8601),
	            'end'		=> 	$entity->getEndTime()->format(\DateTime::ISO8601),
				'event'		=> 	new EventBriefResource($entity->getEvent())
				);
		
		parent::__construct($object, $object['id']);
		
		$selfLink = new Link('self');
		$selfLink->setRoute('api/event_instances', array('event_instance' => $entity->getId()));

		$this->getLinks()->add($selfLink);
		
	}
}