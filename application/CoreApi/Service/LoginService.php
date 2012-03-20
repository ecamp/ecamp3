<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\User;
use CoreApi\Entity\Login;


class LoginService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject CoreApi\Service\UserService
	 */
	protected $userService;
	
	
	/**
	 * @var Core\Repository\LoginRepository
	 * @Inject Core\Repository\LoginRepository
	 */
	protected $loginRepo;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Logout');
		
	}
	
	
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
		$loginValdator = new \Core\Validator\Entity\LoginValidator($login);
		
		$this->validationFailed(
			! $loginValdator->isValid($form));
		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($this->UnwrapEntity($user));
		
		$this->persist($login);		
		$t->flushAndCommit($s);
		
		return $login->asReadonly();
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$t = $this->beginTransaction();

		$this->remove($login);
		
		$t->flushAndCommit($s);
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
		
		$this->validationFailed(
			! $loginValidator->isValid($form));
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
		
		$t->flushAndCommit($s);
	}
	
	
	public function ForgotPassword($identifier, $s = false)
	{
		/** @var \CoreApi\Entity\Login $user */
		$user = $this->userService->Get($identifier);
		
		if(is_null($user))
		{	return false;	}
		
		$user = $this->UnwrapEntity($user);
		$login = $user->getLogin();
		
		if(is_null($login))
		{	return false;	}
		
		
		$t = $this->beginTransaction();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		$t->flushAndCommit($s);
		
		
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
