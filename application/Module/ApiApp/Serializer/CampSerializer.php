<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\Camp;

class CampSerializer extends BaseSerializer{
	
	public function __invoke(Camp $camp){
		$userSerializer = new UserSerializer($this->mime);
		
		return array(
    		'id' 		=> 	$camp->getId(),
			'href'		=>	$this->getCampHref($camp),
    		'owner'		=>	$userSerializer->getReference($camp->getOwner()), 	
    		//($camp->getOwner() != null) ?  $camp->getOwner()->getId() : null,
    		'creator'	=> 	$camp->getCreator()->getId(),
    		'name'		=> 	$camp->getName(),
    		'title'		=> 	$camp->getTitle()
		);
	}
	
	public function getReference(Camp $camp){
		return array(
			'id'	=>	$camp->getId(),
			'href'	=>	$this->getCampHref($camp)
		);
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