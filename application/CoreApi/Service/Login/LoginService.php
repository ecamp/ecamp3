<?php

namespace CoreApi\Service\Login;


use Core\Validator\Entity\LoginValidator;

use CoreApi\Entity\User;
use CoreApi\Entity\Login;
use CoreApi\Service\ServiceBase;



class LoginService 
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
	
	
	public function Get($s = false)
	{
		$user = $this->userService->get();
	
		if(!is_null($user))
		{	return $user->getLogin()->asReadonly();	}
		
		return null;
	}
	
	
	public function Create(User $user, \Zend_Form $form, $s = false)
	{		
		$this->beginTransaction();
		
		
		$login = new \Core\Entity\Login();
		$loginValdator = new LoginValidator($login);
		
		if(! $loginValdator->isValid($form))
		{	$this->throwValidationException();	}

		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($this->UnwrapEntity($user));
			
		$this->persist($login);
			
		$this->flush();
		$this->commit($s);
			
		return $login;
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$this->beginTransaction();

		$login = $this->UnwrapEntity($login);
		$this->remove($login);
		
		$this->flush();
		$this->commit($s);
	}
	
	
	public function Login($identifier, $password)
	{
		
		/** @var \CoreApi\Entity\User */
		$user = $this->userService->get($identifier);
		
		/** @var \Core\Entity\Login */
		$login = $user->getLogin();
		
		$authAdapter = new \Core\Auth\Adapter($login, $password);
		$result = \Zend_Auth::getInstance()->authenticate($authAdapter);
		
		return $result;
	}
	
	
	public function Logout()
	{
		$this->blockIfInvalid(parent::Logout());
		
		
		\Zend_Auth::getInstance()->clearIdentity();
	}
	
	
	public function ResetPassword($pwResetKey, \Zend_Form $form)
	{
		$this->blockIfInvalid(parent::ResetPassword($pwResetKey, $form));
		
		
		/** @var \Core\Entity\Login $login */
		$login = $this->getLoginByResetKey($pwResetKey);
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
	}
	
	
	public function ForgotPassword($identifier)
	{
		$this->blockIfInvalid(parent::ForgotPassword($identifier));
		
		
		/** @var \Core\Entity\Login $user */
		$login = $this->userService->get($identifier)->getLogin();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		//TODO: Send Mail with Link to Reset Password.
		
		return $resetKey;
	}
}
