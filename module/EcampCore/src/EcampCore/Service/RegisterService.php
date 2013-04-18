<?php

namespace EcampCore\Service;

use CoreApi\App\AppEvent;

use EcampCore\Acl\DefaultAcl;
use EcampCore\Service\Params\Params;

use EcampCore\Entity\User;

/**
 * @method EcampCore\Service\RegisterService Simulate
 */
class RegisterService 
	extends ServiceBase
{
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl(){
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Register');
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Activate');
	}
	
	
	/**
	 * @return EcampCore\Entity\User
	 */
	public function Register(Params $params){
		$user 	= $this->service()->userService()->Create($params);
		$login	= $this->service()->loginService()->Create($user, $params);
		
		//AppEvent::Create(2, array($user->getEmail()));
		
		$activationCode = $user->createNewActivationCode();
		
		// TODO: Send Mail with 
		//		 $activationCode!
		
		return $user;
	}
	
	
	/**
	 * Activate a User
	 *
	 * @param EcampCore\Entity\User|int|string $user
	 * @param string $key
	 *
	 * @return bool
	 */
	public function Activate($userId, $key)
	{
		$user = $this->service()->userService()->Get($userId);
		$success = null;
		
		if(is_null($user)){
			$this->validationFailed();
			$this->addValidationMessage("User not found!");
		}
		else if($user->getState() != User::STATE_REGISTERED){
			$this->validationFailed();
			$this->addValidationMessage("User already activated!");
		}
		else{
			$success = $user->activateUser($key);
		}
		
		if($success == false){
			$this->validationFailed();
			$this->addValidationMessage("Wrong activation key!");
		}
		
		return $success;
	}
	
}
