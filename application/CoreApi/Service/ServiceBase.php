<?php

namespace CoreApi\Service;

use CoreApi\Service\ValidationResponse;


abstract class ServiceBase
	implements \Zend_Acl_Resource_Interface
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	
	/**
	 * @var ValidationException
	 */
	private static $validationException = null;
	
	private static $serviceCounter = 0;
	
	
	public function getResourceId()
	{	return get_class($this);	}
	
	
	
	
	protected function validationFailed()
	{
		if(self::$validationException == null)
		{
			self::$validationException = new ValidationException();
		}
	}
	
	protected function addValidationMessage($message)
	{
		$this->validationFailed();
		self::$validationException->addMessage($message);
	}
	
	
	
	protected function start()
	{
		if(self::$serviceCounter == 0)
		{	self::$validationException = null;	}
		
		self::$serviceCounter++;
	}
	
	public function end()
	{
		self::$serviceCounter--;
		
		if(self::$serviceCounter == 0 && isset(self::$validationException))
		{	throw self::$validationException;	}
		
		if(self::$serviceCounter < 0)
		{	throw new \Exception("Not more ServiceCall to be endet!");	}
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