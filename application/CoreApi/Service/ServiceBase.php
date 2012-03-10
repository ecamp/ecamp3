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
	
	
	protected function throwValidationException($message = null, $code = null, $previous = null)
	{
		$this->rollback();
		
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
	
	protected function validationException()
	{
		$this->em->getConnection()->rollback();
		
		$e = new ValidationException();
		
		
	}
	
	
	protected function blockIfInvalid(ValidationResponse $validationResp)
	{
		if(!$validationResp->isValid())
		{
			$e = new ValidationException();
			$e->validationResp = $validationResp;
			throw $e;
		}
	}
	
	
	protected function beginTransaction()
	{
		$this->em->getConnection()->beginTransaction();
	}
	
	protected function commit($s)
	{
		if($s)
		{
			$this->em->getConnection()->rollback();
		}
		else
		{
			$this->em->getConnection()->commit();
		}
	}
	
	protected function rollback()
	{
		$this->em->getConnection()->rollback();
	}
	
	protected function flush()
	{
		try{
			$this->em->flush();
		}
		catch (\PDOException $e)
		{	
			$this->rollback();
			$this->close();
				
			throw $e;
		}
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