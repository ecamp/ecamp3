<?php

namespace CoreApi\Service\Validation;

class LoginServiceValidation extends \CoreApi\Service\ServiceBase
{
		
	
	/**
	 * @var CoreApi\Service\Operation\UserServiceOperation
	 * @Inject CoreApi\Service\Operation\UserServiceOperation
	 */
	protected $userService;
	
	
	/**
	 * Setup ACL. Is used for manual calls of 'checkACL' and for automatic checking
	 * @see    CoreApi\Service\ServiceBase::setupAcl()
	 * @return void
	 */
	protected function setupAcl()
	{
		$this->getAcl()->allow('guest', $this, 'create');
	}
	
	
	protected function get()
	{
		return \Zend_Auth::getInstance()->hasIdentity();
	}
	
	
	protected function create(\Core\Entity\User $user, \Zend_Form $form)
	{
		$valid = true;
		
		if(! is_null($user->getLogin()))
		{	$valid = false;	}
		
		$login = new \Core\Entity\Login();
		$loginValidator = new \Core\Validate\LoginValidator($login);
		
		if(! $loginValidator->isValid($form))
		{	$valid = false;	}
		
		return $valid;
	}
	
	
	protected function update()
	{
		return false;
	}
	
	protected function delete()
	{
		return false;
	}
	
	protected function login($identifier, $password)
	{
		return true;
	}
	
	protected function logout()
	{
		return true;
	}
	
	protected function resetPassword($pwResetKey, $password)
	{
		return ! is_null($this->getLoginByResetKey($pwResetKey));
	}
	
	protected function forgotPassword($identifier)
	{
		$user = $this->userService->get($identifier);
		
		return ! is_null($user) && ! is_null($user->getLogin());
	}
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 *
	 * @return \Core\Entity\Login
	 */
	protected function getLoginByResetKey($pwResetKey)
	{
		/** @var \Core\Entity\Login $login */
		$login = $this->loginRepo->findOneBy(array('pwResetKey' => $pwResetKey));
	
		return $login;
	}
	
}