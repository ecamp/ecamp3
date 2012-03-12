<?php

namespace Core\Service;


abstract class ServiceBase
	implements \Zend_Acl_Resource_Interface
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	
	
	public function getResourceId()
	{	return get_class($this);	}
	
	
	
	protected function validationFailed()
	{
		ValidationWrapper::validationFailed();
	}
	
	protected function addValidationMessage($message)
	{
		ValidationWrapper::addValidationMessage($message);
	}
	
	
	
	
	
	/**
	 * @return Transaction
	 */
	protected function beginTransaction()
	{
		$t = new Transaction($this->em);
		$t->beginTransaction();
		
		return $t;
		
		$this->em->getConfiguration()->beginTransaction();
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
		
		throw new \Exception("Only Entities can be unwrapped!");
	}
	
	
}