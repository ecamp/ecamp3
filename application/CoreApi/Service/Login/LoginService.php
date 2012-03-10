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
	
	
	/**
	 * @return CoreApi\Entity\Login | NULL
	 */
	public function Get($s = false)
	{
		$user = $this->userService->get();
	
		if(!is_null($user))
		{	return $user->getLogin()->asReadonly();	}
		
		return null;
	}
	
	
	/**
	 * @return CoreApi\Entity\Login
	 */
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
		$this->flushAndCommit($s);
			
		return $login->asReadonly();
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$this->beginTransaction();

		$login = $this->UnwrapEntity($login);
		$this->remove($login);
		
		$this->flushAndCommit($s);
	}
	
	
	public function Login($identifier, $password)
	{
		/** @var \CoreApi\Entity\User */
		$user = $this->userService->get($identifier);
		
		if(!is_null($user))
		{	$user = $this->UnwrapEntity($user);	}
		
		/** @var \Core\Entity\Login */
		if(is_null($user))	{	$login = null;	}
		else				{	$login = $user->getLogin();	}
		
		$authAdapter = new \Core\Auth\Adapter($login, $password);
		$result = \Zend_Auth::getInstance()->authenticate($authAdapter);
		
		return $result;
	}
	
	
	public function Logout()
	{
		\Zend_Auth::getInstance()->clearIdentity();
	}
	
	
	public function ResetPassword($pwResetKey, \Zend_Form $form, $s = false)
	{
		$loginValidator = new \Core\Validate\LoginValidator();
		
		/** @var \Core\Entity\Login $login */
		$login = $this->getLoginByResetKey($pwResetKey);
		
		if(is_null($login) || ! $loginValidator->isValid($form))
		{	$this->throwValidationException();	}
		
		
		$this->beginTransaction();
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
		
		$this->flushAndCommit($s);
	}
	
	
	public function ForgotPassword($identifier, $s = false)
	{
		/** @var \CoreApi\Entity\Login $user */
		$user = $this->userService->get($identifier);
		
		if(is_null($user))
		{	$this->throwValidationException();	}
		
		$user = $this->UnwrapEntity($user);
		$login = $user->getLogin();
		
		if(is_null($login))
		{	$this->throwValidationException();	}
		
		$this->beginTransaction();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		$this->flushAndCommit($s);
		
		
		//TODO: Send Mail with Link to Reset Password.
		
		return $resetKey;
	}
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 * @return \Core\Entity\Login
	 */
	private function getLoginByResetKey($pwResetKey)
	{
		/** @var \Core\Entity\Login $login */
		$login = $this->loginRepo->findOneBy(array('pwResetKey' => $pwResetKey));
	
		return $login;
	}
}
