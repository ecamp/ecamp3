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
			$this->em->getConnection()->rollback();
		else
			$this->em->getConnection()->commit();
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