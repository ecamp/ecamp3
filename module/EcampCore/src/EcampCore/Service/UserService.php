<?php

namespace EcampCore\Service;

use EcampCore\Entity\User;
use EcampLib\Service\Params\Params;
use EcampLib\Service\ServiceBase;

use Zend\Validator\EmailAddress;
use Zend\Paginator\Paginator;

/**
 * @method CoreApi\Service\UserService Simulate
 */
class UserService 
	extends ServiceBase
{
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return EcampCore\Entity\User
	 */
	public function Get($id = null){		
		if(isset($id)){
			$user = $this->getByIdentifier($id);
		} else {
			$user = $this->getContext()->getMe();
		}
		
		return $user;
	}
	
	
	/**
	 * Creates a new User with $username
	 * 
	 * @param string $username
	 * 
	 * @return CoreApi\Entity\User
	 */
	public function Create(Params $params){
		
		$email = $params->getValue('email');
		$user = $this->repo()->userRepository()->findOneBy(array('email' => $email));
		
		if(is_null($user)){
			$user = new User();
			$user->setEmail($email);
			
			$this->persist($user);
		}
			
		if($user->getState() != User::STATE_NONREGISTERED){
			$params->addError('email', "This eMail-Adress is already registered!");
			$this->validationFailed();
		}
		
		$newUserValidator = new \Core\Validator\Entity\NewUserValidator($user);
		$this->validationFailed(!$newUserValidator->applyIfValid($params));

		$userValidator = new \Core\Validator\Entity\UserValidator($user);
		$this->validationFailed(!$userValidator->applyIfValid($params));
		
		$user->setState(User::STATE_REGISTERED);
		$activationCode = $user->createNewActivationCode();
		
		//TODO: Send Mail with Link for activation.
		// $activationCode;
		
			
		return $user;
	}
	
	
	public function Update(Params $params){
		$user = $this->getContext()->getUser();
		
		$userValidator = new \Core\Validator\Entity\UserValidator($user);
		
		$this->validationFailed(
			! $userValidator->applyIfValid($params));	
		
		return $user;
	}
	
	public function Delete(){
		// delete user
		$this->em->remove($this->Get());
	}
    
	
	public function SetImage($data, $mime){
		$image = new \CoreApi\Entity\Image();
		$image->setData($data);
		$image->setMime($mime);
		
		$this->Get()->setImage($image);
	}
	
	
	public function DeleteImage(){
		$this->Get()->setImage(null);
	}
	
	
	
	/**
	 * Returns the User for a MailAddress or a Username
	 *
	 * @param string $identifier
	 * @return EcampCore\Entity\User
	 */
	private function getByIdentifier($identifier){
		$user = null;
		$mailValidator = new EmailAddress();
		
		if($identifier instanceOf User){
			$user = $identifier;
		}
		elseif($mailValidator->isValid($identifier)){
			$user = $this->repo()->userRepository()->findOneBy(array('email' => $identifier));
		} else {
			$user = $this->repo()->userRepository()->find($identifier);
		}
		
		/*if(is_null($user)){
			throw new \Exception("No user found for Identifier: " . $identifier);
		}*/
	
		return $user;		
	}
	
	/**
	 * Get all users and wrap in paginator
	 * @return Zend\Paginator\Paginator
	 */
	public function GetPaginator(){
		
		$query = $this->repo()->userRepository()->createQueryBuilder("u");
		$adapter = new \EcampCore\Paginator\Doctrine($query);
		return new Paginator($adapter);
	}
}