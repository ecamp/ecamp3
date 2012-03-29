<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Entity\User;

/**
 * @method CoreApi\Service\UserService Simulate
 */
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
	 * @Inject Core\Service\CampService
	 */
	protected $campService;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'CreateCamp');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'DeleteCamp');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'UpdateCamp');
		
		$this->acl->allow(DefaultAcl::MEMBER,  $this, 'getFriendsOf');
		$this->acl->allow(DefaultAcl::MEMBER,  $this, 'GetPaginator');
		
		$this->acl->allow(DefaultAcl::MEMBER,  $this, 'getMembershipRequests');
		$this->acl->allow(DefaultAcl::MEMBER,  $this, 'getMembershipInvitations');
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
	 * @return Camp object, if creation was successfull
	 */
	public function CreateCamp(\Zend_Form $form)
	{
		if( ! $this->isCampNameUnique($form->getValue("name")) )
		{
			$form->getElement('name')->addError("Camp with same name already exists.");
			$this->validationFailed();
		}

		/* create camp */
		$camp = $this->campService->Create($form);
		$camp->setOwner($this->contextProvider->getContext()->getMe());
		
		return $camp;
	}
	
/**
	 * Updates a Camp
	 * @return \CoreApi\Entity\Camp
	 */
	public function UpdateCamp(\Zend_Form $form)
	{
		$camp = $this->campService->Get($form->getValue('id'));
		
		if($camp->getOwner() != $this->contextProvider->getContext()->getMe())
			throw new \Exception("No Access");
		
		if( $form->getValue('name') != $camp->getName() )
		{
			if( ! $this->isCampNameUnique($form->getValue("name")) )
			{
				$form->getElement('name')->addError("Camp with same name already exists.");
				$this->validationFailed();
			}
		}
	
		/* update camp */
		$camp = $this->campService->Update($camp, $form);
	
		return $camp;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function DeleteCamp($id)
	{
		$camp = $this->campService->Get($id);
	
		if( $camp == null )
			return false;
	
		if($camp->getOwner() != $this->contextProvider->getContext()->getMe())
			throw new \Exception("No Access");
	
		$this->campService->Delete($camp);
	
		return true;
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
	
	/**
	 * @return bool
	 */
	private function isCampNameUnique($name)
	{
		/* check if camp with same name already exists */
		
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
		->add('from', '\CoreApi\Entity\Camp c')
		->add('where', 'c.owner = ?1 AND c.name = ?2')
		->setParameter(1,$this->contextProvider->getContext()->getMe()->getId())
		->setParameter(2, $name);
	
		$query = $qb->getQuery();
	
		if( count($query->getArrayResult()) > 0 ){
			return false;
		}
	
		return true;
	}
	
	/**
	 * Get all users and wrap in paginator
	 * @return \Zend_Paginator
	 */
	public function GetPaginator()
	{
		$query = $this->em->getRepository("CoreApi\Entity\User")->createQueryBuilder("u");
		$adapter = new \Ecamp\Paginator\Doctrine($query);
		return new \Zend_Paginator($adapter);
	}
	
	/**
	 * @return array
	 */
	public function getMembershipRequests($user){
		
		return $this->userRepo->findMembershipRequestsOf($user);
	}

	/**
	 * @return array
	 */
	public function getMembershipInvitations($user)
	{
		return $this->userRepo->findMembershipInvitations($user);
	}
}