<?php

namespace CoreApi\Service\Operation;

use Core\Entity\User;

class UserServiceOperation 
	extends \CoreApi\Service\Validation\UserServiceValidation
{
	
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return \Core\Entity\User
	 */
	protected function get($id = null)
	{
		if(! parent::get($id))
		{	throw new \Ecamp\ValidationException();	}
		
		
		if(isset($id))
		{	return $this->getByIdentifier($id);	}
		
		/** @var \Core\Entity\Login $user */
		$user = null;
		
		if(\Zend_Auth::getInstance()->hasIdentity())
		{
			$userId = \Zend_Auth::getInstance()->getIdentity();
			$user = $this->userRepo->find($userId);
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
	protected function create(\Zend_Form $form)
	{
		if(! parent::create($form))
		{	throw new \Ecamp\ValidationException();	}
		
		
		$this->em->getConnection()->beginTransaction();
		
		try
		{
			$email = $form->getValue('email');
			$user = $this->userRepo->findOneBy(array('email' => $email));
			
			if(is_null($user))
			{
				$user = new \Core\Entity\User();
				$user->setEmail($email);
		
				$this->em->persist($user);
			}
			
			$userValidator = new \Core\Validate\UserValidator($user);
			$userValidator->apply($form);
			
			$user->setState(User::STATE_REGISTERED);
			
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
	
	
	protected function update(\Zend_Form $form)
	{
		if(! parent::update($form))
		{	throw new \Ecamp\ValidationException();	}
		
		// update user
	}
	
	protected function delete(\Zend_Form $form)
	{
		if(! parent::delete($form))
		{	throw new \Ecamp\ValidationException();	}
		
		// delete user
	}
    
	
	/**
	 * Activate a User
	 * 
	 * @param \Core\Entity\User|int|string $user
	 * @param string $key
	 * 
	 * @return bool
	 */
	protected function activate($user, $key)
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
	
	
	/**
	* Creates a new Camp
	* This method is protected, means it is only available from outside (magic!) if ACL is set properly
	*
	* @param \Entity\Group $group Owner of the new Camp
	* @param \Entity\User $user Creator of the new Camp
	* @param Array $params
	* @return Camp object, if creation was successfull
	* @throws \Ecamp\ValidationException
	*/
	protected function createCamp(\Core\Entity\User $creator, $params)
	{
		$this->em->getConnection()->beginTransaction();
		try
		{
			$camp = $this->campService->create($creator, $params);
				
			$camp->setOwner($creator);
	
			$this->em->persist($camp);
			$this->em->flush();
	
			$this->em->getConnection()->commit();
				
			return $camp;
		}
		catch (\PDOException $e)
		{
			$this->em->getConnection()->rollback();
			$this->em->close();
	
			$form = new \Core\Form\Camp\Create();
			$form->getElement('name')->addError("Name has already been taken.");
				
			throw new \Ecamp\ValidationException($form);
		}
	}
}