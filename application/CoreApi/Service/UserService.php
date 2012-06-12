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
		$this->acl->allow(DefaultAcl::GUEST,  $this, 'Create');
		
		
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
		{	$user = $this->getContext()->getMe();	}
		
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
	
	public function Delete()
	{
		/* probably better goes to ACL later, just copied for now from validator */
		$this->validationFailed( $this->Get()->getId() != $form->getValue('id') );
		
		// delete user
		$this->em->remove($this->Get());
	}
    
	
	public function SetImage($data, $mime)
	{
		$image = new \CoreApi\Entity\Image();
		$image->setData($data);
		$image->setMime($mime);
		
		$this->Get()->setImage($image);
	}
	
	
	public function DeleteImage()
	{
		$this->Get()->setImage(null);
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