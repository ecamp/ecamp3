<?php

namespace CoreApi\Service;

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
	public function create($email, $params)
	{
		$this->em->getConnection()->beginTransaction();
		
		try
		{
			$user = $this->userRepo->findOneBy(array('email' => $email));
				
			if(is_null($user))
			{
				$user = new \Core\Entity\User();
				$user->setEmail($email);
		
				$this->em->persist($user);
			}
				
			if($user->getState() != \Core\Entity\User::STATE_NONREGISTERED)
			{	throw new Exception("This eMail-Adress is already registered!");	}

			if(array_key_exists('username', $params))
			{
				$user->setUsername($params['username']);
				$user->setState(\Core\Entity\User::STATE_REGISTERED);
			}
			
			if(array_key_exists('scoutname', $params))
			{	$user->setScoutname($params['scoutname']);	}
			
			if(array_key_exists('firstname', $params))
			{	$user->setFirstname($params['firstname']);	}
			
			if(array_key_exists('surname', $params))
			{	$user->setSurname($params['surname']);	}
			

			$this->em->flush();
			$this->em->getConnection()->commit();
				
			return $user;
		}
		catch (\Exception $e)
		{
			$this->em->getConnection()->rollback();
				
			throw $e;
		}
	}
	
	
	public function update(){	throw new \Exception("Not implemented exception");	}
	public function delete(){	throw new \Exception("Not implemented exception");	}
    
	
	/**
	 * Activate a User
	 * 
	 * @param \Core\Entity\User|int|string $user
	 * @param string $key
	 * 
	 * @return bool
	 */
	public function activate($user, $key)
	{
		$user = $this->get($user);
		
		if(is_null($user))
		{
			return false;
		}
		
		if($user->getState() != \Core\Entity\User::STATE_REGISTERED)
		{
			return false;
		}
		
		return $user->activateUser($key);
	}
	
	
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