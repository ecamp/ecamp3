<?php

namespace CoreApi\Service\User;

use Core\Entity\User;
use CoreApi\Service\ServiceBase;


class UserServiceValidator
	extends ServiceBase
{
	
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	protected $userRepo;
	
	/**
	 * @var CoreApi\Service\User\UserService
	 * @Inject CoreApi\Service\User\UserService
	 */
	protected $userService;
	
	/**
	* @var CoreApi\Service\Camp
	* @Inject CoreApi\Service\Camp
	*/
	// protected $campService;
	
	
	
	
	/**
	 * Get User is allways valid
	 * @param \Core\Entity\User|string|int $id
	 */
	public function Get($id = null)
	{
		return true;
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
		$valid = true;
		
		$email = $form->getValue('email');
		$user = $this->userRepo->findOneBy(array('email' => $email));
		
		if(is_null($user))
		{
			$user = new \Core\Entity\User();
			$user->setEmail($email);
		}
			
		if($user->getState() != \Core\Entity\User::STATE_NONREGISTERED)
		{		
			$form->getElement('email')->addError("This eMail-Adress is already registered!");
			$valid = false;
		}
		
		$userValidator = new \Core\Validate\UserValidator($user);
		$valid &= $userValidator->isValid($form);
		
		return $valid;
	}
	
	
	public function Update(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		return $valid;
	}
	
	
	public function Delete(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		return $valid;	
	}
	
	
	public function Activate($user, $key)
	{
		$user = $this->get($user);
		
		if(is_null($user))
		{	return false;	}
		
		if($user->getState() != \Core\Entity\User::STATE_REGISTERED)
		{	return false;	}
		
		return true;
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
	protected function CreateCamp(\Core\Entity\User $creator, \Zend_Form $form)
	{
// 		$this->em->getConnection()->beginTransaction();
// 		try
// 		{
// 			$camp = $this->campService->create($creator, $params);
				
// 			$camp->setOwner($creator);
	
// 			$this->em->persist($camp);
// 			$this->em->flush();
	
// 			$this->em->getConnection()->commit();
				
// 			return $camp;
// 		}
// 		catch (\PDOException $e)
// 		{
// 			$this->em->getConnection()->rollback();
// 			$this->em->close();
	
// 			$form = new \Core\Form\Camp\Create();
// 			$form->getElement('name')->addError("Name has already been taken.");
				
// 			throw new \Ecamp\ValidationException($form);
// 		}

		return true;
	}
}