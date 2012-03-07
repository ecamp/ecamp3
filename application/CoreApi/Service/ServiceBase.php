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
	
	
	protected function UnwrappEntity(\CoreApi\Entity\BaseEntity $entity)
	{
		return \Core\Entity\Wrapper\Unwrapper::Unwrapp($entity);
	}
}