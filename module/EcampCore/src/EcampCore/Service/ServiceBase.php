<?php

namespace EcampCore\Service;

use Zend\Permissions\Acl\Resource\ResourceInterface;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

use EcampCore\Entity\BaseEntity;
use EcampCore\Acl\DefaultAcl;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
abstract class ServiceBase implements 
	ServiceLocatorAwareInterface,
	ResourceInterface
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
	
	protected function locateService($name){
		return $this->serviceLocator->get($name);
	}
	
	
	
	public function __call($method, $args){
		if($this->serviceLocator->has('__repos__.' . $method)){
			return $this->serviceLocator->get('__repos__.' . $method);
		}
	
		if($this->serviceLocator->has('__services__.' . $method)){
			return $this->serviceLocator->get('__internal_services__.' . $method);
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
			$this->acl = $this->serviceLocator->get('ecamp.internal.acl');
		}
		return $this->acl;
	}
	
	
	/**
	 * @return EcampCore\Acl\ContextProvider
	 */
	protected function getContextProvider(){
		return $this->locateService('ecamp.acl.contextprovider');
	}
	
	/**
	 * @return EcampCore\Acl\Context
	 */
	protected function getContext(){
		return $this->locateService('ecamp.acl.context');
	}
	
	
	/**
	 * Setup ACL
	 *
	 * @return void
	 */
	abstract public function _setupAcl();
	
	public function getResourceId(){
		return get_class($this);
	}
	
	
	
// 	protected function validationFailed($bool = true, $message = null)
// 	{
// 		if($bool && $message == null)
// 			ServiceWrapper::validationFailed();
		
// 		if($bool && $message != null)
// 			ServiceWrapper::addValidationMessage($message);
// 	}
	
// 	protected function validationAssert($bool = false, $message = null)
// 	{
// 		if(!$bool && $message == null)
// 			ServiceWrapper::validationFailed();
		
// 		if(!$bool && $message != null)
// 			ServiceWrapper::addValidationMessage($message);
// 	}
	
// 	protected function validationContextAssert(BaseEntity $entity)
// 	{
// 		if(! $this->getContext()->Check($entity))
// 		{
// 			ServiceWrapper::addValidationMessage(
// 				get_class($entity) . " with ID (" . $entity->getId() . 
// 				") does not belong to any Entity in the Context."
// 			);
// 		}
// 	}
	
// 	protected function addValidationMessage($message)
// 	{
// 		ServiceWrapper::addValidationMessage($message);
// 	}
	
// 	protected function hasFailed()
// 	{
// 		return ServiceWrapper::hasFailed();
// 	}
	
// 	protected function persist($entity)
// 	{
// 		$this->em->persist($entity);
// 	}
	
// 	protected function remove($entity)
// 	{
// 		$this->em->remove($entity);
// 	}
	
}