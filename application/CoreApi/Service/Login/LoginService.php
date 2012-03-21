<?php

namespace CoreApi\Service\Login;

use Core\Service\ServiceBase;

use Core\Entity\User;
use Core\Entity\Login;

use Core\Acl\DefaultAcl;

class LoginService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\User\UserService
	 * @Inject CoreApi\Service\User\UserService
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
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Logout');
	}
	
	/**
	 * @return Core\Entity\Login | NULL
	 */
	public function Get($s = false)
	{
		$user = $this->userService->get();
		
		if(!is_null($user))
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	/**
	 * @return Core\Entity\Login
	 */
	public function Create(User $user, \Zend_Form $form, $s = false)
	{
		$t = $this->beginTransaction();
		
		$login = new \Core\Entity\Login();
		$loginValdator = new LoginValidator($login);
		
		if(! $loginValdator->isValid($form))
		{	$this->validationFailed();	}
		
		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($user);
		
		$this->persist($login);
		$this->flush();
		
		$t->flushAndCommit($s);
		
		return $login;
	}
	
	
	public function Delete(Login $user, $s = false)
	{
		$t = $this->beginTransaction();

		$this->remove($login);
		$this->flush();
		
		$t->flushAndCommit($s);
	}
	
	
	/**
	 * @return Zend_Auth_Result 
	 */
	public function Login($identifier, $password)
	{
		/** @var \Core\Entity\User */
		$user = $this->userService->get($identifier);
		
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
		$t->flushAndCommit($s);
	}
	
	
	public function ForgotPassword($identifier, $s = false)
	{
		/** @var \Core\Entity\Login $user */
		$user = $this->userService->get($identifier);
		
		if(is_null($user))
		{	return false;	}
		
		$login = $user->getLogin();
		
		if(is_null($login))
		{	return false;	}
		
		
		$t = $this->beginTransaction();
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		$this->flush();
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
