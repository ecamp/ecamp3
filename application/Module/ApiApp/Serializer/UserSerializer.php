<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\User;

class UserSerializer extends BaseSerializer{
	
	public function __invoke(User $user){
		return array(
			'id'		=> 	$user->getId(),
			'href'		=>	$this->getUserHref($user),
			'username'	=>	$user->getUsername(),
			'email'		=>	$user->getEmail(),
			'scoutname'	=>	$user->getScoutname(),
			'firstname'	=>	$user->getFirstname(),
			'surname'	=>	$user->getSurname()
		);
	}
	
	public function getReference(User $user = null){
		
			return array(
				'id'	=>	$user->getId(),
				'href'	=>	$this->getUserHref($user)
			);
		
	}
	
	private function getUserHref(User $user){
		return 
			$this->router->assemble(
				array(
					'id' => $user->getId(), 
					'mime' => $this->mime
				), 
				'api.v1.user'
			);
	}
	
}