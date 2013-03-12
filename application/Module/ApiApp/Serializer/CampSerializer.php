<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\Camp;

class CampSerializer extends BaseSerializer{
	
	public function __invoke(Camp $camp){
		$userSerializer = new UserSerializer($this->mime);
		$periodSerializer = new PeriodSerializer($this->mime);
		$eventSerializer = new EventSerializer($this->mime);
		
		return array(
    		'id' 		=> 	$camp->getId(),
			'href'		=>	$this->getCampHref($camp),
    		'owner'		=>	$userSerializer->getReference($camp->getOwner()),
    		'creator'	=> 	$userSerializer->getReference($camp->getCreator()),
    		'name'		=> 	$camp->getName(),
    		'title'		=> 	$camp->getTitle(),
    		'periods'	=> 	$periodSerializer->getCollectionReference($camp),
    		'events'	=>	$eventSerializer->getCollectionReference($camp), 
		);
	}
	
	public function getReference(Camp $camp = null){
		if($camp == null){
			return null;
		} else {
			return array(
				'id'	=>	$camp->getId(),
				'href'	=>	$this->getCampHref($camp)
			);
		}
	}
	
	private function getCampHref(Camp $camp){
		return
			$this->router->assemble(
			array(
				'id' => $camp->getId(),
				'mime' => $this->mime
			), 
			'api.v1.camp'
		);
	}
}