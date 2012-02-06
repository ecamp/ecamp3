<?php

namespace CoreApi\Service;


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
	
	
	protected function blockIfInvalid($bool)
	{
		if(!$bool)
		{	throw new \InvalidArgumentException("Invalid Arguemnts for Service call.");	}
	}
	
	
	protected function beginTransaction()
	{
		$this->em->getConnection()->beginTransaction();
	}
	
	protected function commitTransaction()
	{
		$this->em->getConnection()->commit();
	}
	
	protected function rollbackTransaction()
	{
		$this->em->getConnection()->rollback();
	}
	
	protected function flush()
	{
		$this->em->flush();
	}
	
	protected function persistEntity($entity)
	{
		$this->em->persist($entity);
	}
	
	protected function removeEntity($entity)
	{
		$this->em->remove($entity);
	}
}