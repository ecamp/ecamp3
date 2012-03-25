<?php

namespace CoreApi\Service;

class Login extends ServiceAbstract
{
	
	/**
	 * @var \Core\Repository\UserRepository
	 * @Inject \Core\Repository\UserRepository
	 */
	private $userRepo;
	
	
	/**
	 * @var \Doctrine\ORM\EntityRepository
	 * @Inject \Core\Repository\LoginRepository
	 */
	private $loginRepo;
	
	
	/**
	 * @var \CoreApi\Service\User
	 * @Inject \CoreApi\Service\User
	 */
	private $userService;
	
	
	/**
	 * This returns the authenticated Login Instance
	 * 
	 * @return \CoreApi\Entity\Login
	 */
	public function get()
	{
		$user = $this->userService->get();
		
		if(!is_null($user))
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	/**
	 * Creates a Login Entity for a User
	 * 
	 * @param \CoreApi\Entity\User $user
	 * @param string @password
	 * 
	 * @return \CoreApi\Entity\Login
	 */
	public function create(\CoreApi\Entity\User $user, \Zend_Form $form)
	{
		/** @var \CoreApi\Entity\Login $login */
		$login = $user->getLogin();
		
		if(!is_null($login))
		{	throw new \Exception("User has allready a Login!");	}
		
		
		$this->em->getConnection()->beginTransaction();
		
		try 
		{
			$login = new \CoreApi\Entity\Login();
			$loginValidator = new \Core\Validate\LoginValidator($login);
			
			if($loginValidator->isValid($form))
			{
				$password = $form->getValue('password');
				$login->setNewPassword($password);
			}
			else
			{
				throw new \Ecamp\ValidationException();
			}
			
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
	
	
	public function update(){	throw new \Exception("Not Implemented");	}
	public function delete(){	throw new \Exception("Not Implemented");	}
	
	
	/**
	 * Logs a User in.
	 * (Creates its Session)
	 * 
	 * @return \Zend_Auth_Result
	 */
	public function login($identifier, $password)
	{
		/** @var \CoreApi\Entity\User */
		$user = $this->userService->get($identifier);
		
		/** @var \CoreApi\Entity\Login */
		$login = $user->getLogin();
		
		$authAdapter = new \CoreApi\Service\Auth\Adapter($login, $password);
		$result = \Zend_Auth::getInstance()->authenticate($authAdapter);
		
		return $result;
	}
	
	
	/**
	 * Logs the User out.
	 * (Deletes its Session)
	 */
	public function logout()
	{
		\Zend_Auth::getInstance()->clearIdentity();
	}
	
	
	/**
	 * Reset Password
	 */
	public function resetPassword($pwResetKey, $password)
	{
		/** @var \CoreApi\Entity\Login $login */
		$login = $this->getLoginByResetKey($pwResetKey);
		
		if(!is_null($login))
		{
			$login->setNewPassword($password);
			$login->clearPwResetKey();
		}
		
		throw new \Ecamp\PermissionException("SecurityKey was wrong");
	}
	
	
	/**
	 * Sends a Mail a given Mailadress with a link 
	 * to reset to reset the Password
	 *
	 * @param string $login
	 */
	public function forgotPassword($identifier)
	{
		/** @var \CoreApi\Entity\User $user */
		$user = $this->userService->get($identifier);
		
		if(!is_null($user->getLogin()))
		{
			$user->getLogin()->createPwResetKey();
			$resetKey = $user->getLogin()->getPwResetKey();
			
			// TODO: Send Mail with Link to Reset Password.
			
			return $resetKey;
		}
		
		throw new \Exception("Identifier not found!");
	}
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 * 
	 * @return \CoreApi\Entity\Login
	 */
	private function getLoginByResetKey($pwResetKey)
	{
		/** @var \CoreApi\Entity\Login $login */
		$login = $this->loginRepo->findOneBy(array('pwResetKey' => $pwResetKey));
		
		return $login;
	}
	
}