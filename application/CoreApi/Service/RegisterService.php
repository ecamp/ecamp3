<?php

namespace CoreApi\Service;

use Core\Service\Transaction;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\User;

/**
 * @method CoreApi\Service\RegisterService Simulate
 */
class RegisterService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	protected $userService;
	
	
	/**
	 * @var CoreApi\Service\LoginService
	 * @Inject CoreApi\Service\LoginService
	 */
	protected $loginService;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Register');
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Activate');
	}
	
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function Register(\Zend_Form $registerForm)
	{
		$user 	= $this->userService->Create($registerForm, $s);
		$login	= $this->loginService->Create($user, $registerForm, $s);
		
		$activationCode = $user->createNewActivationCode();
		
		// TODO: Send Mail with 
		//		 $activationCode!
		
		return $user;
	}
	
	
	/**
	 * Activate a User
	 *
	 * @param \CoreApi\Entity\User|int|string $user
	 * @param string $key
	 *
	 * @return bool
	 */
	public function Activate($userId, $key)
	{
		$user = $this->userService->Get($userId);
		$success = null;
		
		if(is_null($user))
		{
			$this->validationFailed();
			$this->addValidationMessage("User not found!");
		}
		else if($user->getState() != User::STATE_REGISTERED)
		{
			$this->validationFailed();
			$this->addValidationMessage("User already activated!");
		}
		else{
			$success = $user->activateUser($key);
		}
		
		return $success;
	}
	
}
