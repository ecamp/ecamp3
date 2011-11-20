<?php

namespace Core\Service;

class User extends ServiceAbstract
{
	
	/**
	* @var \Core\Repository\UserRepository
	* @Inject \Core\Repository\UserRepository
	*/
	private $userRepo;
	
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return \Core\Entity\User
	 */
	public function get($id = null)
	{
		if(isset($id))
		{	return $this->getByIdentifier($id);	}
		
		/** @var \Core\Entity\Login $user */
		$user = null;
		
		if(\Zend_Auth::getInstance()->hasIdentity())
		{
			$userId = \Zend_Auth::getInstance()->getIdentity();
			$user = $this->$userRepo->find($userId);
		}
		
		return $user;
	}
	
	
	/**
	 * Creates a new User with $username
	 * 
	 * @param string $username
	 * 
	 * @return \Core\Entity\User
	 */
	public function create($username)
	{
		$user = new \Core\Entity\User($username);
		
		$this->em->persist($user);
		return $user;
	}
	
	
	public function update(){	throw new \Exception("Not implemented exception");	}
	public function delete(){	throw new \Exception("Not implemented exception");	}
    
	/**
	 * 
	 * Return the set of roles for the current user based on the context (Group, Camp)
	 * @param unknown_type $group
	 * @param unknown_type $camp
	 */
	public function getCurrentUserRole($group = null, $camp = null){
		$roles = array();
		$roles[] = new \Zend_Acl_Role('member');
		$roles[] = new \Zend_Acl_Role('group_manager');
		$roles[] = new \Zend_Acl_Role('camp_normal');
		
		return $roles;
	}
	
	
	
	
	/**
	* Returns the User for a MailAddress or a Username
	*
	* @param string $identifier
	*
	* @return \Core\Entity\User
	*/
	private function getByIdentifier($identifier)
	{
		$user = null;
		
		$mailValidator = new \Zend_Validate_EmailAddress();
		
		if($identifier instanceOf \Core\Entity\User)
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