<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Entity\User;


class UserService 
	extends ServiceBase
{
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	protected $userRepo;
	
	/**
	 * @var CoreApi\Service\CampService
	 * @Inject CoreApi\Service\CampService
	 */
	protected $campService;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'CreateCamp');

		$this->acl->allow(DefaultAcl::IN_SERVICE,  $this, 'Create');
	}
	
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return CoreApi\Entity\User
	 */
	public function Get($id = null)
	{		
		if(isset($id))
		{	$user = $this->getByIdentifier($id);	}
		else
		{	$user = $this->contextProvider->getContext()->getMe();	}
		
		return $user;
	}
	
	
	/**
	 * Creates a new User with $username
	 * 
	 * @param string $username
	 * 
	 * @return CoreApi\Entity\User
	 */
	public function Create(\Zend_Form $form)
	{	
		$email = $form->getValue('email');
		$user = $this->userRepo->findOneBy(array('email' => $email));
		
		if(is_null($user))
		{
			$user = new User();
			$user->setEmail($email);
			
			$this->persist($user);
		}
			
		if($user->getState() != User::STATE_NONREGISTERED)
		{		
			$form->getElement('email')->addError("This eMail-Adress is already registered!");
			$this->validationFailed();
		}
		
		$userValidator = new \Core\Validator\Entity\UserValidator($user);
		$this->validationFailed( 
			! $userValidator->applyIfValid($form) );	
		
		$user->setState(User::STATE_REGISTERED);
		$activationCode = $user->createNewActivationCode();
		
		//TODO: Send Mail with Link for activation.
		// $activationCode;
		
			
		return $user;
	}
	
	
	public function Update(\Zend_Form $form)
	{
		/* probably better goes to ACL later, just copied for now from validator */
		$this->validationFailed( $this->Get()->getId() != $form->getValue('id') );
		
		// update user
	}
	
	public function Delete(\Zend_Form $form)
	{
		/* probably better goes to ACL later, just copied for now from validator */
		$this->validationFailed( $this->Get()->getId() != $form->getValue('id') );
		
		// delete user
	}
    
	
	
	/**
	 * Creates a new Camp
	 * This method is protected, means it is only available from outside (magic!) if ACL is set properly
	 *
	 * @param \Entity\Group $group Owner of the new Camp
	 * @param \Entity\User $user Creator of the new Camp
	 * @param Array $params
	 * @return Camp object, if creation was successfull
	 */
	public function CreateCamp(\Zend_Form $form)
	{
		/* check if camp with same name already exists */
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
		->add('from', '\CoreApi\Entity\Camp c')
		->add('where', 'c.owner = ?1 AND c.name = ?2')
		->setParameter(1,$this->contextProvider->getContext()->getMe()->getId())
		->setParameter(2, $form->getValue('name'));
		
		$query = $qb->getQuery();
		
		if( count($query->getArrayResult()) > 0 ){
			$form->getElement('name')->addError("Camp with same name already exists.");
			$this->validationFailed();
		}

		/* create camp */
		$camp = $this->campService->Create($form, $s);
		$camp->setOwner($this->contextProvider->getContext()->getMe());
		
		return $camp;
	}
	
	
	/**
	 * Returns the User for a MailAddress or a Username
	 *
	 * @param string $identifier
	 *
	 * @return \CoreApi\Entity\User
	 */
	private function getByIdentifier($identifier)
	{
		$user = null;
		
		$mailValidator = new \Zend_Validate_EmailAddress();
		
		if($identifier instanceOf User)
		{
			$user = $identifier;
		}
		elseif($mailValidator->isValid($identifier))
		{
			$user = $this->userRepo->findOneBy(array('email' => $identifier));
		}
		elseif(is_numeric($identifier))
		{
			$user = $this->userRepo->find($identifier);
		}
		else
		{
			$user = $this->userRepo->findOneBy(array('username' => $identifier));
		}
		
		if(is_null($user))
		{
			throw new \Exception("No user found for Identifier: " . $identifier);
		}
	
		return $user;		
	}
}