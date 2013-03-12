<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\User;
use CoreApi\Entity\UserCamp;

class ContributorSerializer extends BaseSerializer{
	
	public function __invoke(UserCamp $uc){
		$userSerializer = new UserSerializer($this->mime);
		$campSerializer = new CampSerializer($this->mime);
		
		return array(
			'id'		=> 	$uc->getId(),
			'href'		=>	$this->getContributorHref($uc),
			'user'		=>	$userSerializer->getReference($uc->getUser()),
			'camp'		=>	$campSerializer->getReference($uc->getCamp()),
			'role'		=>	$uc->getRole()
		);
	}
	
	private function getContributorHref(UserCamp $userCamp){
		return 
			$this->router->assemble(
				array(
					'id' => $userCamp->getId(), 
					'mime' => $this->mime
				), 
				'api.v1.contributor'
			);
	}
}