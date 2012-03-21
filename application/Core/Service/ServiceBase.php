<?php

namespace Core\Service;

use Core\Acl\DefaultAcl;

abstract class ServiceBase
	implements \Zend_Acl_Resource_Interface
{
	
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
	 * @var Core\Acl\Context
	 * @Inject Core\Acl\Context
	 */
	protected $context;
	
	/**
	 * Setup ACL
	 *
	 * @return void
	 */
	abstract protected function _setupAcl();
	
	public function postInject()
	{
		$this->_setupACL();
	}
	
	public function getResourceId()
	{	return get_class($this);	}
	
	
	
	protected function validationFailed($bool = true)
	{
		if($bool)
			ValidationWrapper::validationFailed();
	}
	
	protected function addValidationMessage($message)
	{
		ValidationWrapper::addValidationMessage($message);
	}
	
	protected function hasFailed()
	{
		return ValidationWrapper::hasFailed();
	}
	
	
	
	/**
	 * @return Transaction
	 */
	protected function beginTransaction()
	{
		$t = new Transaction($this->em);
		$t->beginTransaction();
		
		return $t;
	}
	
	
	
	protected function persist($entity)
	{
		$entity = $this->UnwrapEntity($entity);
		$this->em->persist($entity);
	}
	
	protected function remove($entity)
	{
		$entity = $this->UnwrapEntity($entity);
		$this->em->remove($entity);
	}
	
	protected function flush()
	{
		$this->em->flush();
	}
	
	
	
	protected function UnwrapEntity($entity)
	{
		if($entity instanceof \CoreApi\Entity\BaseEntity)
		{	return \Core\Entity\Wrapper\Unwrapper::Unwrapp($entity);	}
		
		if($entity instanceof \Core\Entity\BaseEntity)
		{	return $entity;	}
		
		if(is_null($entity))
		{	return null;	}
		
		throw new \Exception("Only Entities can be unwrapped!");
	}
	
	
}