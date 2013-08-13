<?php
namespace EcampApi\Camp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Camp;
use EcampApi\User\UserResource;

class CampBriefResource extends HalResource{
	
	public function __construct(Camp $camp){
		
		$object = array(
				"id" => $camp->getId(),
				"name" => $camp->getName(),
				"title" => $camp->getTitle(),
				"start" => "1.1.1970", /* TBD */
				"end" => "1.1.1970"
				);
		
		parent::__construct($object, $object['id']);
		
		$selfLink = new Link('self');
		$selfLink->setRoute('api/camps', array('camp' => $camp->getId()));
		
		$this->getLinks()->add($selfLink);
		
	}
}