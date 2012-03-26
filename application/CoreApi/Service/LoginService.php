<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Entity\User;
use CoreApi\Entity\Login;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
class LoginService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject Core\Service\UserService
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
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		
		$this->acl->allow(DefaultAcl::GUEST, $this, 'Login');
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
	public function Create(User $user, \Zend_Form $form)
	{
		$login = new Login();
		$loginValdator = new \Core\Validator\Entity\LoginValidator($login);
		
		$this->validationFailed(
			! $loginValdator->isValid($form));
		
		$login->setNewPassword($form->getValue('password'));
		$login->setUser($user);
		
		$this->persist($login);		
		
		return $login;
	}
	
	
	public function Delete(Login $user)
	{
		$this->remove($login);
	}
	
	
	/**
	 * @return Zend_Auth_Result 
	 */
	public function Login($identifier, $password)
	{
		/** @var CoreApi\Entity\User */
		$user = $this->userService->get($identifier);
		
		/** @var CoreApi\Entity\Login */
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
	
	
	public function ResetPassword($pwResetKey, \Zend_Form $form)
	{
		$login = $this->getLoginByResetKey($pwResetKey);
		$loginValidator = new \Core\Validate\LoginValidator($login);
		
		if(is_null($login))
		{	$this->addValidationMessage("No Login found for given PasswordResetKey");	}
		
		$this->validationFailed(
			! $loginValidator->isValid($form));
		
		$login->setNewPassword($form->getValue('password'));
		$login->clearPwResetKey();
	}
	
	
	public function ForgotPassword($identifier)
	{
		/** @var CoreApi\Entity\Login $user */
		$user = $this->userService->Get($identifier);
		
		if(is_null($user))
		{	return false;	}
		
		$login = $user->getLogin();
		
		if(is_null($login))
		{	return false;	}
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		
		//TODO: Send Mail with Link to Reset Password.
		
		
		return $resetKey;
	}
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 * @return CoreApi\Entity\Login
	 */
	private function getLoginByResetKey($pwResetKey)
	{
		/** @var \CoreApi\Entity\Login $login */
		$login = $this->loginRepo->findOneBy(array('pwResetKey' => $pwResetKey));
	
		return $login;
	}
}
