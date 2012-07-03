<?php

namespace CoreApi\Service;

use Core\Job\RegisterJobs;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Service\Params\Params;

use CoreApi\Entity\User;

/**
 * @method CoreApi\Service\RegisterService Simulate
 */
class RegisterService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject Core\Service\UserService
	 */
	protected $userService;
	
	
	/**
	 * @var CoreApi\Service\LoginService
	 * @Inject Core\Service\LoginService
	 */
	protected $loginService;
	
	
	/**
	 * @var Coreapi\Service\JobService
	 * @Inject Core\Service\JobService
	 */
	protected $jobService;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Register');
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Activate');
	}
	
	
	/**
	 * @return CoreApi\Entity\User
	 */
	public function Register(Params $params)
	{
		$user 	= $this->userService->Create($params);
		$login	= $this->loginService->Create($user, $params);
		
		$activationCode = $user->createNewActivationCode();
		
		$jobParams = array(
			'user_id' => $user->getId(),
			'activationCode' => $activationCode
		);
		$this->jobService->AddJob(RegisterJobs::SendActivationCode($jobParams));
		
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
		
		if($success == false)
		{
			$this->validationFailed();
			$this->addValidationMessage("Wrong activation key!");
		}
		
		return $success;
	}
	
}
