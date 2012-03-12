<?php

namespace CoreApi\Service\User;

use Core\Entity\User;


class UserService 
	extends UserServiceValidator
{
	
	/**
	* Setup ACL
	* @return void
	*/
	protected function _setupAcl()
	{
		$this->_acl->allow('user_me', $this, 'createCamp');
	}
	
	/**
	 * Returns the User with the given Identifier
	 * (Identifier can be a MailAddress, a Username or a ID)
	 * 
	 * If no Identifier is given, the Authenticated User is returned
	 * 
	 * @return \Core\Entity\User
	 */
	public function Get($id = null)
	{	
		
		/** @var \Core\Entity\Login $user */
		$user = null;
		
		if(isset($id))
		{	return $this->getByIdentifier($id);	}
		
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
	public function Create(\Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Create($form));
		
		
		$this->beginTransaction();
		
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
			
			$this->flush();
			$this->commit();
				
			return $user;
		}
		catch (\Exception $e)
		{
			$this->rollback();
				
			throw $e;
		}
	}
	
	
	public function Update(\Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Update($form));
		
		// update user
	}
	
	public function Delete(\Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Delete($form));
		
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
	public function Activate($user, $key)
	{
		$this->blockIfInvalid(parent::Activate($user, $key));
		
		return $this->get($user)->activateUser($key);
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
	public function createCamp(\Core\Entity\User $creator, \Zend_Form $form, $s = false)
	{
		$this->assertAccess( $this->getRoles(array('user' => $creator)), 'createCamp' );
		
		$respObj = $this->getRespObj($s)->beginTransaction();
		
		/* check if camp with same name already exists */
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
		->add('from', '\Core\Entity\Camp c')
		->add('where', 'c.owner = ?1 AND c.name = ?2')
		->setParameter(1,$creator->getId())
		->setParameter(2, $form->getValue('name'));
		
		$query = $qb->getQuery();
		
		if( count($query->getArrayResult()) > 0 ){
			$form->getElement('name')->addError("Camp with same name already exists.");
			$respObj->validationFailed(true);
		}
		
		/* create camp */
		$camp = $respObj( $this->campService->Create($creator, $form, $s) )->getReturn();	
		$camp = $this->UnwrapEntity($camp);
		$camp->setOwner($creator);
			
		$respObj->flushAndCommit();
		return $respObj($camp);
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
	
	
	public function getRoles($context)
	{
		$roles = array();
		$me = $this->Get();
		
		foreach( $context as $key => $value ){
			switch($key){
				case 'user':
					if( $me == $value )
						$roles[] = 'user_me';
					break;
					
				case 'camp':
					if( $value->isOwner($me) )
						$roles[] = 'camp_owner';
					break;
			}
		}
	
		return $roles;
	}
}