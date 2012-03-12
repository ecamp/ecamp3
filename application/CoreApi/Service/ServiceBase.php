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
	 * ACL object
	 *
	 * @var App_Model_Acl
	 */
	protected $_acl;
	
	/**
	 * Resource ID of the service
	 *
	 * @var string
	 */
	protected $_resource;
	
	/**
	 * Setup ACL
	 *
	 * @return void
	 */
	protected function _setupAcl()
	{
	}
	
	/**
	 * Get ACL
	 *
	 * @return Zend_Acl
	 */
	public function getAcl()
	{
		if (null === $this->_acl) {
			$this->_acl = new \Core\Acl\DefaultAcl();
			$this->_acl->add($this);
	
			$this->_setupAcl();
		}
	
		return $this->_acl;
	}
	
	/**
	 * Throws an Exception if $roles has no access to $privilege
	 *
	 * @return boolean
	 */
	public function assertAccess($roles, $privilege){
		if( !$this->checkAcl($roles, $privilege) )
			throw new \Ecamp\PermissionException("");
	}
	
	/**
	 * Check if a specific set of roles has access to the given resource/privilege
	 *
	 * @return boolean
	 */
	public function checkAcl($roles, $privilege)
	{
		foreach( $roles as $role) {
			if( $this->getAcl()->isAllowed($role, $this, $privilege) )
				return true;
		}
		 
		return false;
	}
	
	/**
	 * @see    Zend_Acl_Resource_Interface::getResourceId()
	 * @return string
	 */
	public function getResourceId()
	{
		return $this->_resource;
	}

	
	
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