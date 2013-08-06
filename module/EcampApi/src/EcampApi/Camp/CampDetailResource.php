<?php
namespace EcampApi\Camp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Camp;
use EcampApi\User\UserBriefResource;

class CampDetailResource extends HalResource{
	
	public function __construct(Camp $camp){
		
		$object = array(
				"id" => $camp->getId(),
				"name" => $camp->getName(),
				"title" => $camp->getTitle(),
				"creator" => new UserBriefResource($camp->getCreator())
				);
		
		parent::__construct($object, $object['id']);
		
		$selfLink = new Link('self');
		$selfLink->setRoute('api/camps', array('camp' => $camp->getId()));
		
		$eventsLink = new Link('events');
		$eventsLink->setUrl('api/events/TBD');
		
		$this->getLinks()->add($selfLink)
						 ->add($eventsLink);
		
	}
}