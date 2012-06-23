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
	abstract public function _setupAcl();
	
	public function getResourceId()
	{	return get_class($this);	}
	
	/**
	 * @return CoreApi\Acl\Context
	 */
	public function getContext()
	{
		return $this->contextProvider->getContext();
	}
	
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
	
	protected function persist($entity)
	{
		$this->em->persist($entity);
	}
	
	protected function remove($entity)
	{
		$this->em->remove($entity);
	}
	
}