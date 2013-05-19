<?php

namespace EcampCore\Service;



use Zend\Authentication\AuthenticationService;

use Zend\Permissions\Acl\Resource\ResourceInterface;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use EcampCore\Entity\User;
use EcampCore\Entity\BaseEntity;
use EcampCore\ServiceUtil\ServiceWrapper;
use EcampCore\Acl\DefaultAcl;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
abstract class ServiceBase implements 
	ServiceLocatorAwareInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	private $serviceLocator;
	
	/**
	 * @see Zend\ServiceManager.ServiceLocatorAwareInterface::setServiceLocator()
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	/**
	 * @return Zend\ServiceManager\ServiceLocatorInterface
	 */
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	
	
	public function __call($method, $args){
		if($this->serviceLocator->has('__repos__.' . $method)){
			return $this->serviceLocator->get('__repos__.' . $method);
		}
	
		if($this->serviceLocator->has('__services__.' . $method)){
			return $this->serviceLocator->get('__services__.' . $method);
		}
	}
	
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	protected function getEM(){
		if($this->em == null){
			$this->em = $this->serviceLocator->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}
	
	
	/** @var EcampCore\Acl\DefaultAcl */
	private $acl;
	
	/**
	 * @return EcampCore\Acl\DefaultAcl 
	 */
	protected function getAcl(){
		if($this->acl == null){
			$this->acl = $this->serviceLocator->get('ecampcore.internal.acl');
		}
		return $this->acl;
	}
	
	/**
	 * @param User $user
	 * @param BaseEntity $entity
	 * @param $privilege
	 * @throws EcampCore\Acl\Exception\NoAccessException
	 */
	protected function aclRequire(User $user, BaseEntity $entity, $privilege){
		$this->_setupAcl();
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
	
	
	
	
	/**
	 * Setup ACL
	 *
	 * @return void
	 */
	abstract public function _setupAcl();
	
	
	
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
	
	
	protected function persist($entity){
		$this->getEM()->persist($entity);
	}
	
	protected function remove($entity){
		$this->getEM()->remove($entity);
	}
	
}