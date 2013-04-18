<?php

namespace EcampCore\Service;

use Zend\Authentication\AuthenticationService;

use EcampCore\Acl\DefaultAcl;
use EcampCore\Acl\ContextProvider;
use EcampCore\Repository\LoginRepository;
use EcampCore\Service\Params\Params;

use EcampCore\Entity\User;
use EcampCore\Entity\Login;


/**
 * @method EcampCore\Service\LoginService Simulate
 */
class LoginService 
	extends ServiceBase
{
	
	/**
	 * Setup ACL
	 */
	public function _setupAcl(){
		$this->getAcl()->allow(DefaultAcl::GUEST,  $this, 'Create');
		$this->getAcl()->allow(DefaultAcl::MEMBER, $this, 'Get');
		
		$this->getAcl()->allow(DefaultAcl::GUEST,  $this, 'Login');
		$this->getAcl()->allow(DefaultAcl::MEMBER, $this, 'Logout');
	}
	
	
	/**
	 * @return EcampCore\Entity\Login | NULL
	 */
	public function Get(){
		$user = $this->service()->userService()->get();
		
		if(!is_null($user))
		{	return $user->getLogin();	}
		
		return null;
	}
	
	
	/**
	 * @return EcampCore\Entity\Login
	 */
	public function Create(User $user, Params $params){
		$login = new Login();
		$loginValdator = new \Core\Validator\Entity\LoginValidator($login);
		
		$this->validationFailed(
			! $loginValdator->isValid($params));
		
		$login->setNewPassword($params->getValue('password'));
		$login->setUser($user);
		$this->persist($login);
		
		return $login;
	}
	
	
	public function Delete(){
		$me = $this->getContextProvider()->getMe();
		$login = $me->getLogin();
		
		if(is_null($login))
		{	$this->addValidationMessage("There is no Login to be deleted!");	}
		else
		{	$this->remove($login);	}
	}
	
	
	/**
	 * @return Zend\Authentication\Result 
	 */
	public function Login($identifier, $password){
		
		/** @var EcampCore\Entity\User  */
		$user = $this->service()->userService()->get($identifier);
		
		if(is_null($user))	{	
			return null;
		} else {
			/** @var EcampCore\Entity\Login */
			$login = $user->getLogin();
		}
		
		$authAdapter = new \EcampCore\Auth\Adapter($login, $password);
		$authService = new AuthenticationService();
		$result = $authService->authenticate($authAdapter);
		
		$this->getContextProvider()->reset();
		
		return $result;
	}
	
	
	public function Logout(){
		
		$authService = new AuthenticationService();
		$authService->clearIdentity();
		
		$this->getContextProvider()->reset();
	}
	
	
	public function ResetPassword($pwResetKey, Params $params){
		
		$login = $this->getLoginByResetKey($pwResetKey);
		$loginValidator = new \EcampCore\Validate\LoginValidator($login);
		
		if(is_null($login)){
			$this->addValidationMessage("No Login found for given PasswordResetKey");
		}
		
		$this->validationFailed(
			! $loginValidator->isValid($params));
		
		$login->setNewPassword($params->getValue('password'));
		$login->clearPwResetKey();
	}
	
	
	public function ForgotPassword($identifier){
		
		$user = $this->service()->userService()->Get($identifier);
		
		if(is_null($user)){
			return false;
		}
		
		$login = $user->getLogin();
		
		if(is_null($login)){
			return false;
		}
		
		$login->createPwResetKey();
		$resetKey = $login->getPwResetKey();
		
		
		//TODO: Send Mail with Link to Reset Password.
		
		
		return $resetKey;
	}
	
	
	/**
	 * Returns the LoginEntity with the given pwResetKey
	 *
	 * @param string $pwResetKey
	 * @return EcampCore\Entity\Login
	 */
	private function getLoginByResetKey($pwResetKey){
		
		/** @var \EcampCore\Entity\Login $login */
		$login = $this->repo()->loginRepository()->findOneBy(array('pwResetKey' => $pwResetKey));
	
		return $login;
	}
}
