<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\User;


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
	public function Register(\Zend_Form $registerForm, $s = false)
	{
		$t = $this->beginTransaction();
		
		$user 	= $this->userService->Create($registerForm, $s);
		$login	= $this->loginService->Create($user, $registerForm, $s);
		
		$activationCode = $user->createNewActivationCode();
		
		// TODO: Send Mail with 
		//		 $activationCode!
		
		
		$t->flushAndCommit($s);
		
		
		// TODO: Remove this code!!
			if(\Core\Service\ValidationWrapper::hasFailed())
			{	return;	}
			
			$link = "/register/activate/" . $user->getId() . "/key/" . $activationCode;
			echo "<a href='" . $link . "'>" . $link . "</a>";
			die();
		
		
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
	public function Activate($userId, $key, $s = false)
	{
		$t = $this->beginTransaction();
		
		$user = $this->userService->Get($userId);
		
		if(is_null($user))
		{
			$this->validationFailed();
			$this->addValidationMessage("User not found!");
		}
		
		if($user->getState() != User::STATE_REGISTERED)
		{
			$this->validationFailed();
			$this->addValidationMessage("User already activated!");
		}
		
		$success = $user->activateUser($key);
		$t->flushAndCommit($s);
		
		return $success;
	}
	
}
