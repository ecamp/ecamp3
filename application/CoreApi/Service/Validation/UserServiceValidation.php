<?php

namespace CoreApi\Service\Validation;

class UserServiceValidation extends \CoreApi\Service\ServiceBase
{
	
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	protected $userRepo;
	
	/**
	 * @var CoreApi\Service\Operation\UserServiceOperation
	 * @Inject CoreApi\Service\Operation\UserServiceOperation
	 */
	protected $userService;
	
	/**
	* @var CoreApi\Service\Camp
	* @Inject CoreApi\Service\Camp
	*/
	// protected $campService;
	
	
	
	/**
	 * Setup ACL. Is used for manual calls of 'checkACL' and for automatic checking
	 * @see    CoreApi\Service\ServiceBase::setupAcl()
	 * @return void
	 */
	protected function setupAcl()
	{
		$this->getAcl()->allow('guest', $this, 'create');
		$this->getAcl()->allow('guest', $this, 'activate');
	}
	
	/**
	 * Get User is allways valid
	 * @param \Core\Entity\User|string|int $id
	 */
	protected function get($id = null)
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
	protected function create(\Zend_Form $form)
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

		if(! $userValidator->isValid($form))
		{
			$valid = false;
		}
		
		return $valid;
	}
	
	
	protected function update(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		return $valid;
	}
	
	
	protected function delete(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		return $valid;	
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