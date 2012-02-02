<?php

namespace CoreApi\Service\Operation;

class LoginServiceOperation 
	extends \CoreApi\Service\Validation\LoginServiceValidation
{
	
	/**
	 * This returns the authenticated Login Instance
	 *
	 * @return \Core\Entity\Login
	 */
	protected function get()
	{
		if(! parent::get())
		{	throw new \Ecamp\ValidationException();	}
		

		$user = $this->userService->get();
	
		if(!is_null($user))
		{
			return $user->getLogin();
		}
	
		return null;
	}
	
	
	/**
	 * Creates a Login Entity for a User
	 *
	 * @param \Core\Entity\User $user
	 * @param string @password
	 *
	 * @return \Core\Entity\Login
	 */
	protected function create(\Core\Entity\User $user, \Zend_Form $form)
	{
		if(! parent::create($user, $form))
		{	throw new \Ecamp\ValidationException();	}
		
	
		$this->em->getConnection()->beginTransaction();
	
		try
		{
			$login = new \Core\Entity\Login();
			$loginValidator = new \Core\Validate\LoginValidator($login);
				
			$password = $form->getValue('password');
			$login->setNewPassword($password);	
			$login->setUser($user);
				
			$this->em->persist($login);
				
			$this->em->flush();
			$this->em->getConnection()->commit();
			
			return $login;
		}
		catch (\Exception $e)
		{
			$this->em->getConnection()->rollback();
				
			throw $e;
		}
	}
	
	protected function update()
	{
		if(! parent::update())
		{	throw new \Ecamp\ValidationException();	}
		
		
		throw new \Exception("Not Implemented");
	}
	
	
	protected function delete()
	{
		if(! parent::delete())
		{	throw new \Ecamp\ValidationException();	}
		
		
		throw new \Exception("Not Implemented");
	}
	
	
	/**
	 * Logs a User in.
	 * (Creates its Session)
	 *
	 * @return \Zend_Auth_Result
	 */
	protected function login($identifier, $password)
	{
		if(! parent::login($identifier, $password))
		{	throw new \Ecamp\ValidationException();	}
		
		
		/** @var \Core\Entity\User */
		$user = $this->userService->get($identifier);
	
		/** @var \Core\Entity\Login */
		$login = $user->getLogin();
	
		$authAdapter = new \CoreApi\Service\Auth\Adapter($login, $password);
		$result = \Zend_Auth::getInstance()->authenticate($authAdapter);
	
		return $result;
	}
	
	
	/**
	 * Logs the User out.
	 * (Deletes its Session)
	 */
	protected function logout()
	{
		if(! parent::logout())
		{	throw new \Ecamp\ValidationException();	}
		
		
		\Zend_Auth::getInstance()->clearIdentity();
	}
	
	
	/**
	 * Sends a Mail a given Mailadress with a link
	 * to reset to reset the Password
	 *
	 * @param string $login
	 */
	public function forgotPassword($identifier)
	{
		if(! parent::forgotPassword($identifier))
		{	throw new \Ecamp\ValidationException();	}

		
		/** @var \Core\Entity\User $user */
		$user = $this->userService->get($identifier);

		$user->getLogin()->createPwResetKey();
		$resetKey = $user->getLogin()->getPwResetKey();
			
		// TODO: Send Mail with Link to Reset Password.
			
		return $resetKey;
	}
	
	
	/**
	 * Reset Password; Password lost functionality
	 */
	protected function resetPassword($pwResetKey, $password)
	{
		if(!parent::resetPassword($pwResetKey, $password))
		{	throw new \Ecamp\ValidationException();	}
		
		
		/** @var \Core\Entity\Login $login */
		$login = $this->getLoginByResetKey($pwResetKey);
		
		$login->setNewPassword($password);
		$login->clearPwResetKey();
	}
		
		
	
}