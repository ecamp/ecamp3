<?php
namespace EcampApi\User;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\User;

class UserDetailResource extends HalResource{
	
	public function __construct(User $user){
		
		$object = array(
				"id" => $user->getId(),
				"username" => $user->getUsername(),
				"fullname" => $user->getFullName(),
				"email"    => $user->getEmail()
				);
		
		parent::__construct($object, $object['id']);
		
		$selfLink = new Link('self');
		$selfLink->setRoute('api/users', array('user' => $user->getId()));
		
		$campsLink = new Link('camps');
		$campsLink->setRoute('api/users/camps', array('user' => $user->getId()));
		
		$this->getLinks()->add($selfLink)
						 ->add($campsLink);
	}
}