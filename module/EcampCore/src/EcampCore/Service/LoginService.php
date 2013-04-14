<?php

namespace EcampCore\Service;

use Zend\Authentication\AuthenticationService;

use EcampCore\Acl\ContextProvider;
use EcampCore\Repository\LoginRepository;
use EcampCore\Service\Params\Params;

use EcampCore\Entity\User;
use EcampCore\Entity\Login;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
class LoginService 
	extends ServiceBase
{
	
	/** 
	 * @return Zend\Authentication\AuthenticationService 
	 */
	private function getAuthService(){
		return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
	}	
	
	/** 
	 * @return EcampCore\Service\UserService
	 */
	private function getUserService(){
		return $this->getServiceLocator()->get('ecamp.service.user');
	}
	
	/**
	 * Setup ACL
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		
		$this->acl->allow(DefaultAcl::GUEST,  $this, 'Login');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Logout');
	}
	
	
	/**
	 * @return CoreApi\Entity\Login | NULL
	 */
	public function Get(){
		$user = $this->getUserService()->get();
		
		if(!is_null($user))
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	/**
	 * @return CoreApi\Entity\Login
	 */
	public function Create(User $user, Params $params)
	{
		$login = new Login();
		$loginValdator = new \Core\Validator\Entity\LoginValidator($login);
		
		$this->validationFailed(
			! $loginValdator->isValid($params));
		
		$login->setNewPassword($params->getValue('password'));
		$login->setUser($user);
		$this->persist($login);
		
		return $login;
	}
	
	
	public function Delete()
	{
		$me = $this->getContextProvider()->getMe();
		$login = $me->getLogin();
		
		if(is_null($login))
		{	$this->addValidationMessage("There is no Login to be deleted!");	}
		else
		{	$this->remove($login);	}
	}
	
	
	/**
	 * @return Zend_Auth_Result 
	 */
	public function Login($identifier, $password)
	{
		/** @var CoreApi\Entity\User */
		$user = $this->getUserService()->get($identifier);
		
		/** @var CoreApi\Entity\Login */
		if(is_null($user))	{	return null;	}
		else				{	$login = $user->getLogin();	}
		
		$authAdapter = new \EcampCore\Auth\Adapter($login, $password);
		$result = $this->getAuthService()->authenticate($authAdapter);
		
		$this->getContextProvider()->reset();
		
		return $result;
	}
	
	
	public function Logout(){
		$this->getAuthService()->clearIdentity();
		$this->getContextProvider()->reset();
	}
	
	
	public function ResetPassword($pwResetKey, Params $params)
	{
		$login = $this->getLoginByResetKey($pwResetKey);
		$loginValidator = new \Core\Validate\LoginValidator($login);
		
		if(is_null($login))
		{	$this->addValidationMessage("No Login found for given PasswordResetKey");	}
		
		$this->validationFailed(
			! $loginValidator->isValid($params));
		
		$login->setNewPassword($params->getValue('password'));
		$login->clearPwResetKey();
	}
	
	
	public function ForgotPassword($identifier)
	{
		$user = $this->getUserService()->Get($identifier);
		
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
