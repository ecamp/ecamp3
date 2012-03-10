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
		$respObj = $this->getRespObj($s);
		
		$user = $respObj($this->userService->get())->getReturn();
		
		if(!is_null($user))
		{	return $respObj($user->getLogin());	}
		
		$respObj->validationFailed();
		return $respObj;
	}
	
	
	/**
	 * @return CoreApi\Entity\Login
	 */
	public function Create(User $user, \Zend_Form $form, $s = false)
	{
		$respObj = $this->getRespObj($s)->beginTransaction();
		
		
		$login = new \Core\Entity\Login();
		$loginValdator = new LoginValidator($login);
		
		$respObj->validationFailed(! $loginValdator->isValid($form));
		
		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($this->UnwrapEntity($user));
		
		$this->persist($login);
		
		
		$respObj->flushAndCommit();
		return $respObj($login);
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$respObj = $this->getRespObj($s)->beginTransaction();

		$login = $this->UnwrapEntity($login);
		$this->remove($login);
		
		return $respObj->flushAndCommit();
	}
	
	
	public function Login($identifier, $password)
	{
		$respObj = $this->getRespObj(false);
		
		/** @var \CoreApi\Entity\User */
		$user = $respObj($this->userService->get($identifier))->getReturn();
		
		/** @var \Core\Entity\Login */
		if(is_null($user))	{	$login = null;	}
		else				{	$login = $this->UnwrapEntity($user)->getLogin();	}
		
		$authAdapter = new \Core\Auth\Adapter($login, $password);
		$result = \Zend_Auth::getInstance()->authenticate($authAdapter);
		
		return $respObj($result);
	}
	
	
	public function Logout()
	{
		\Zend_Auth::getInstance()->clearIdentity();

		return $this->getRespObj(false);
	}
	
	
	public function ResetPassword($pwResetKey, \Zend_Form $form, $s = false)
	{
		$respObj = $this->getRespObj($s)->beginTransaction();
		
		$loginValidator = new \Core\Validate\LoginValidator();
		
		/** @var \Core\Entity\Login $login */
		$login = $this->getLoginByResetKey($pwResetKey);
		
		$respObj->validationFailed(
			is_null($login) || ! $loginValidator->isValid($form));
		
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
		
		return $respObj->flushAndCommit();
	}
	
	
	public function ForgotPassword($identifier, $s = false)
	{
		$respObj = $this->getRespObj($s);
		
		
		/** @var \CoreApi\Entity\Login $user */
		$user = $this->userService->get($identifier);
		
		if(is_null($user))
		{	$respObj->validationFailed();	return $respObj;	}
		
		$user = $this->UnwrapEntity($user);
		$login = $user->getLogin();
		
		if(is_null($login))
		{	$respObj->validationFailed();	return $respObj;	}
		
		
		$respObj->beginTransaction();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		$respObj->flushAndCommit();
		
		
		//TODO: Send Mail with Link to Reset Password.
		
		return $respObj($resetKey);
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
