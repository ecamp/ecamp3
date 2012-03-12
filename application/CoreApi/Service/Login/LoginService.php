<?php

namespace CoreApi\Service\Login;


use Core\Service\ServiceBase;
use Core\Validator\Entity\LoginValidator;

use CoreApi\Entity\User;
use CoreApi\Entity\Login;



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
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	/**
	 * @return CoreApi\Entity\Login
	 */
	public function Create(User $user, \Zend_Form $form, $s = false)
	{
		$t = $this->beginTransaction();
		
		$login = new \Core\Entity\Login();
		$loginValdator = new LoginValidator($login);
		
		if(! $loginValdator->isValid($form))
		{	$this->validationFailed();	}
		
		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($this->UnwrapEntity($user));
		
		$this->persist($login);
		$this->flush();
		
		$t->commit($s);
		
		return $login->asReadonly();
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$t = $this->beginTransaction();

		$this->remove($login);
		$this->flush();
		
		$t->commit($s);
		$this->flush();
	}
	
	
	/**
	 * @return Zend_Auth_Result 
	 */
	public function Login($identifier, $password)
	{
		/** @var \CoreApi\Entity\User */
		$user = $this->userService->get($identifier);
		
		/** @var \Core\Entity\Login */
		if(is_null($user))	{	$login = null;	}
		else				{	$login = $this->UnwrapEntity($user)->getLogin();	}
		
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
		$t = $this->beginTransaction();
		
		$login = $this->getLoginByResetKey($pwResetKey);
		$loginValidator = new \Core\Validate\LoginValidator($login);
		
		if(is_null($login))
		{	$this->addValidationMessage("No Login found for given PasswordResetKey");	}
		
		if(! $loginValidator->isValid($form))
		{	$this->validationFailed();	}
		
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
		
		$this->flush();
		$t->commit($s);
	}
	
	
	public function ForgotPassword($identifier, $s = false)
	{
		/** @var \CoreApi\Entity\Login $user */
		$user = $this->userService->get($identifier);
		
		if(is_null($user))
		{	return false;	}
		
		$login = $user->getLogin();
		$login = $this->UnwrapEntity($login);
		
		if(is_null($login))
		{	return false;	}
		
		
		$t = $this->beginTransaction();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		$this->flush();
		$t->commit($s);
		
		
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
