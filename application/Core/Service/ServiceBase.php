<?php

namespace Core\Service;

use CoreApi\Entity\BaseEntity;
use Core\Acl\DefaultAcl;


abstract class ServiceBase
	implements \Zend_Acl_Resource_Interface
{
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	protected $kernel;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	protected $acl;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * Setup ACL
	 *
	 * @return void
	 */
	abstract public function _setupAcl();
	
	public function getResourceId()
	{	return get_class($this);	}
	
	/**
	 * @return CoreApi\Acl\Context
	 */
	protected function getContext()
	{
		return $this->contextProvider->getContext();
	}
	
	protected function validationFailed($bool = true, $message = null)
	{
		if($bool && $message == null)
			ServiceWrapper::validationFailed();
		
		if($bool && $message != null)
			ServiceWrapper::addValidationMessage($message);
	}
	
	protected function validationAssert($bool = false, $message = null)
	{
		if(!$bool && $message == null)
			ServiceWrapper::validationFailed();
		
		if(!$bool && $message != null)
			ServiceWrapper::addValidationMessage($message);
	}
	
	protected function validationContextAssert(BaseEntity $entity)
	{
		if(! $this->getContext()->Check($entity))
		{
			ServiceWrapper::addValidationMessage(
				get_class($entity) . " with ID (" . $entity->getId() . 
				") does not belong to any Entity in the Context."
			);
		}
	}
	
	protected function addValidationMessage($message)
	{
		ServiceWrapper::addValidationMessage($message);
	}
	
	protected function hasFailed()
	{
		return ServiceWrapper::hasFailed();
	}
	
	protected function persist($entity)
	{
		$this->em->persist($entity);
	}
	
	protected function remove($entity)
	{
		$this->em->remove($entity);
	}
	
	public function asJob()
	{
		$jobFactory = new JobFactory($this);
		$this->kernel->Inject($jobFactory);
		
		return $jobFactory;
	}
	
}