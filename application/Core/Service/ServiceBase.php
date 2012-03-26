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
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
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
			ServiceWrapper::validationFailed();
	}
	
	protected function addValidationMessage($message)
	{
		ServiceWrapper::addValidationMessage($message);
	}
	
	protected function hasFailed()
	{
		return ServiceWrapper::hasFailed();
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
		$this->em->persist($entity);
	}
	
	protected function remove($entity)
	{
		$this->em->remove($entity);
	}
	
	protected function flush()
	{
		$this->em->flush();
	}
}