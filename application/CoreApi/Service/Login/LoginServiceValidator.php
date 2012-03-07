<?php

namespace CoreApi\Service\Login;

use Core\Entity\User;
use CoreApi\Service\ServiceBase;
use CoreApi\Service\ValidationResponse;


class LoginServiceValidator
	extends ServiceBase
{
	
	/**
	 * @var \CoreApi\Service\User\UserService
	 * @Inject \CoreApi\Service\User\UserService
	 */
	protected $userService;
	
	/**
	 * @var Core\Repository\LoginRepository
	 * @Inject Core\Repository\LoginRepository
	 */
	protected $loginRepo;
	
	
	public function Get()
	{
		if(\Zend_Auth::getInstance()->hasIdentity())
		{
			return new ValidationResponse(true);
		}
		else
		{
			$validationResp = new ValidationResponse();
			$validationResp->addMessage("You are not logged in!");
		}
	}
	
	public function Create(User $user, \Zend_Form $form)
	{
		$loginValidator = new \Core\Validate\LoginValidator();
		
		$valid = true;
		
		$valid &= !is_null($user->getLogin());
		$valid &= $loginValidator->isValid($form);
		
		return new ValidationResponse($valid);
	}
	
	
	public function Delete()
	{
		return false;
	}
	
	
	public function Login($identifier, $password)
	{
		$user = $this->userService->get($identifier);
		
		$valid =  true;
		$valid &= !is_null($user);
		$valid &= !is_null($user->getLogin());
		
		return new ValidationResponse($valid);
	}
	
	
	public function Logout()
	{
		return new ValidationResponse(true);
	}
	
	
	public function ResetPassword($pwResetKey, \Zend_Form $form)
	{
		$loginValidator = new \Core\Validate\LoginValidator();
		
		$valid =  true;
		$valid &= !is_null($this->getLoginByResetKey($pwResetKey));
		$valid &= $loginValidator->isValid($form);
		
		return new ValidationResponse($valid);
	}
	
	
	public function ForgotPassword($identifier)
	{
		$user = $this->userService->get($identifier);
		
		$valid =  true;
		$valid &= !is_null($user);
		$valid &= !is_null($user->getLogin());
		
		return new ValidationResponse($valid);
	}
	
	
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 * @return \Core\Entity\Login
	 */
	protected function getLoginByResetKey($pwResetKey)
	{
		/** @var \Core\Entity\Login $login */
		$login = $this->loginRepo->findOneBy(array('pwResetKey' => $pwResetKey));
		
		return $login;
	}
}
