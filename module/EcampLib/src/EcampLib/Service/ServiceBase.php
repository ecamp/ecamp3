<?php

namespace EcampLib\Service;

use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;

use EcampCore\Entity\User;
use EcampCore\Entity\BaseEntity;
use EcampCore\ServiceUtil\ServiceWrapper;
use EcampLib\Acl\Acl;

abstract class ServiceBase 
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEntityManager(){
		return $this->em;
	}
	
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}

	protected function persist($entity){
		$this->getEntityManager()->persist($entity);
	}
	
	protected function remove($entity){
		$this->getEntityManager()->remove($entity);
	}
	
	
	
	/** 
	 * @var EcampLib\Acl\Acl 
	 */
	private $acl;
	
	/**
	 * @return EcampLib\Acl\Acl 
	 */
	public function getAcl(){
		return $this->acl;
	}
	
	public function setAcl(Acl $acl){
		$this->acl = $acl;
	}
	
	/**
	 * @param User $user
	 * @param BaseEntity $entity
	 * @param $privilege
	 * @throws EcampCore\Acl\Exception\NoAccessException
	 */
	protected function aclRequire(User $user, BaseEntity $entity, $privilege){
		$this->getAcl()->isAllowedException($user, $entity, $privilege);
	}
	
	
	/**
	 * @return EcampCore\Entity\User
	 */
	protected function me(){
		$auth = new AuthenticationService();
		$id = $auth->getIdentity();
		
		if($id){
			$userRepo = $this->getServiceLocator()->get('ecampcore.repo.user'); 
			return $userRepo->find($id);
		} else {
			return null;
		}
	}
	
	
	
	protected function validationFailed($bool = true, $message = null){
		
		if($bool && $message == null){
			ServiceWrapper::validationFailed();
		}
		
		if($bool && $message != null){
			ServiceWrapper::addValidationMessage($message);
		}
	}
	
	protected function validationAssert($bool = false, $message = null)
	{
		if(!$bool && $message == null)
			ServiceWrapper::validationFailed();
		
		if(!$bool && $message != null)
			ServiceWrapper::addValidationMessage($message);
	}
	
	protected function addValidationMessage($message){
		ServiceWrapper::addValidationMessage($message);
	}
	
	protected function hasFailed(){
		return ServiceWrapper::hasFailed();
	}
	
	
	
}