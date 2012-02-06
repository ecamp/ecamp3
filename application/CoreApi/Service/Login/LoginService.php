<?php

namespace CoreApi\Service\Login;

use Core\Entity\User;


class LoginService 
	extends LoginServiceValidator
{
	
	public function Get()
	{
		$this->blockIfInvalid(parent::Get());
		
		$user = $this->userService->get();
		
		if(!is_null($user))
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	public function Create(User $user, \Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Create($user, $form));
		
		
		$this->beginTransaction();
		
		try 
		{
			$login = new \Core\Entity\Login();
			$login->setNewPassword($form->getValue('password'));
			$login->setUser($user);
			
			$this->em->persist($login);
			
			$this->flush();
			$this->commit();
			
			return $login;
		}
		catch (\Exception $e)
		{
			$this->rollback();
			
			throw $e;
		}
	}
	
	
	public function Delete()
	{
		assert(false);
	}
	
	
	public function Login($identifier, $password)
	{
		$this->blockIfInvalid(parent::Login($identifier, $password));
		
		
		/** @var \Core\Entity\User */
		$user = $this->userService->get($identifier);
		
		/** @var \Core\Entity\Login */
		$login = $user->getLogin();
		
		$authAdapter = new \CoreApi\Service\Auth\Adapter($login, $password);
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
