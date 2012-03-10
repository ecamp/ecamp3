<?php

namespace CoreApi\Service\User;

use Core\Entity\User;
use CoreApi\Service\ServiceBase;
use CoreApi\Service\ValidationResponse;


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
	 * @var CoreApi\Service\Camp\CampService
	 * @Inject CoreApi\Service\Camp\CampService
	 */
	protected $campService;
	
	/**
	 * @var CoreApi\Service\Camp\CampServiceValidator
	 * @Inject CoreApi\Service\Camp\CampServiceValidator
	 */
	protected $campServiceValidator;
	

	
	/**
	 * Get User is allways valid
	 * @param \Core\Entity\User|string|int $id
	 */
	public function Get($id = null)
	{
		return new ValidationResponse(true);
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
		
		return new ValidationResponse($valid);
	}
	
	
	public function Update(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		
		return new ValidationResponse($valid);
	}
	
	
	public function Delete(\Zend_Form $form)
	{
		$valid = ($this->userService->get()->getId() == $form->getValue('id')); 
		
		return new ValidationResponse($valid);	
	}
	
	
	public function Activate($user, $key)
	{
		$user = $this->get($user);
		
		if(is_null($user))
		{
			$validationResp = new ValidationResponse(false);
			$validationResp->addMessage("User not found!");
		}
		
		if($user->getState() != \Core\Entity\User::STATE_REGISTERED)
		{
			$validationResp = new ValidationResponse(false);
			$validationResp->addMessage("User already activated!");
		}
		
		return new ValidationResponse(true);
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
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
			->add('from', '\Core\Entity\Camp c')
			->add('where', 'c.owner = ?1 AND c.name = ?2')
			->setParameter(1,$creator->getId())
			->setParameter(2, $form->getValue('name'));
		
		$query = $qb->getQuery();
		
		if( count($query->getArrayResult()) > 0 ){
			$form->getElement('name')->addError("Camp with same name already exists.");
			return new ValidationResponse(false);
		}
		
		return $this->campServiceValidator->Create($creator, $form);;
	}
}