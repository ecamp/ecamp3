<?php

namespace EcampCore\Service;

use EcampCore\Entity\User;

use EcampLib\Service\ServiceBase;
use EcampLib\Service\Params\Params;

/**
 * @method EcampCore\Service\RegisterService Simulate
 */
class RegisterService 
	extends ServiceBase
{
	
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
