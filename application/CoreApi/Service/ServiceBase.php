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
	
	
	public function getResourceId()
	{	return get_class($this);	}
	
	
	/**
	 * @return ServiceResponse
	 */
	public function getRespObj($s)
	{
		return new ServiceResponse($this->em, $s);
	}
	
	
	protected function throwValidationException($message = null, $code = null, $previous = null)
	{
		$this->rollbackAll();
		
		// empty exception:
		if(is_null($message))
		{	throw new ValidationException();	}
		
		// exeption:
		if($message instanceof \Exception)
		{	throw new $message;	}
		
		// string:
		if(is_string($message))
		{	throw new ValidationException($message, $code, $previous);	}
		
		// array:
		if(is_array($message))
		{
			$e = new ValidationException();
			
			foreach ($messages as $key => $message)
			{	$e->addMessage($key, $message);	}
			
			throw $e;
		}
	}
	
	
	protected function beginTransaction()
	{
		$this->em->getConnection()->beginTransaction();
	}
	
	protected function flushAndCommit($s)
	{
		if($s)
		{
			$this->em->getConnection()->rollback();
		}
		else
		{
			try{
				$this->em->flush();
				$this->em->getConnection()->commit();
			}
			catch (\PDOException $e)
			{	
				$this->rollbackAll();
				$this->close();
					
				throw $e;
			}
		}
	}
	
	protected function rollback()
	{
		$this->em->getConnection()->rollback();
	}
	
	protected function rollbackAll()
	{
		$i = $this->em->getConnection()->getTransactionNestingLevel();
		for(; $i>0; $i--)
			$this->em->getConnection()->rollback();
	}
	
	protected function persist($entity)
	{
		$this->em->persist($entity);
	}
	
	protected function remove($entity)
	{
		$this->em->remove($entity);
	}
	
	protected function close($entity)
	{
		$this->em->close($entity);
	}
	
	
	protected function UnwrapEntity(\CoreApi\Entity\BaseEntity $entity)
	{
		return \Core\Entity\Wrapper\Unwrapper::Unwrapp($entity);
	}
}