<?php

namespace Core\Service;

use CoreApi\Entity\Job;

class ServiceWrapper
	implements \Zend_Acl_Resource_Interface
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	private $kernel;
	
	/**
	 * @var Core\Acl\DefaultAcl
	 * @Inject Core\Acl\DefaultAcl
	 */
	private $acl;
	
	/**
	 * The protected Resource
	 * @var Zend_Acl_Resource_Interface
	 */
	private $service = null;
	
	/**
	 * @var ValidationException
	 */
	private static $validationException = null;
	
	private $transaction = null;
	
	public static $simulated = false;
	
	public function __construct(\Zend_Acl_Resource_Interface $service)
	{
		$this->service = $service;
	}
	
	public function postInject()
	{
		$this->acl->addResource($this->service);
		$this->kernel->Inject($this->service);
	
		unset($this->kernel);
	}
	
	public static function validationFailed()
	{
		if(self::$validationException == null)
		{
			self::$validationException = new ValidationException();
		}
	}
	
	public static function addValidationMessage($message)
	{
		self::validationFailed();
		self::$validationException->addMessage($message);
	}
	
	public static function hasFailed()
	{
		return self::$validationException != null;
	}
	
	public static function isSimulated()
	{
		return self::$simulated;
	}
	
	public function getResourceId()
	{
		return $this->service->getResourceId();
	}
	
	public function __call($method, $args)
	{
		if( !method_exists($this->service, $method) )
		{
			throw new \Exception("Method $method does not exist.");
		}
		
		$this->service->_setupAcl();
		
		if( ! $this->isAllowed($method) )
			throw new \Exception("No Access on " . $this->service->getResourceId() . "::" . $method);
			
		$this->start();
		
		$r = call_user_func_array(array($this->service, $method), $args);
		
		$this->end();
		
		return $r;
	}
	 
	public function Simulate()
	{
		return new ServiceSimulator($this);
	}
	
	public function AsBackgroundJob()
	{
		$job = new Job();
		$job->setClass(get_class($this->service));
		
		$this->em->persist($job);
		
		return $job;
	}
	
	private function start()
	{
		self::$validationException = null;
		
		$uow = $this->em->getUnitOfWork();
		$uow->computeChangeSets();
		
		$upd = $uow->getScheduledEntityUpdates();
		$ins = $uow->getScheduledEntityInsertions();
		$del = $uow->getScheduledEntityDeletions();
		$colupd = $uow->getScheduledCollectionUpdates();
		$coldel = $uow->getScheduledCollectionDeletions();
		
		if( !empty($upd) || !empty($ins)|| !empty($del)|| !empty($colupd)|| !empty($coldel) )
			throw new \Exception("You tried to edit an entity outside the service layer.");
		
		$this->transaction = $this->em->getConnection()->beginTransaction();
	}
	
	private function end()
	{
		$this->flushAndCommit();
		
		if(isset(self::$validationException))
		{
			throw self::$validationException;
		}
	}
	
	private function isAllowed($privilege = NULL)
	{
		$roles = $this->acl->getRolesInContext();
	
		foreach ($roles as $role)
		{
			if($this->acl->isAllowed($role, $this->service, $privilege))
			{
				return true;
			}
		}
	
		return false;
	}
	
	private function flushAndCommit()
	{
		if(self::hasFailed() || self::isSimulated() )
		{
			$this->em->getConnection()->rollback();
			
			if( self::isSimulated() )
			{	
				/** TODO: this call is problematic, find a better solution */
				$this->em->clear();
			}
			return;
		}
	
		try
		{
			$this->em->flush();
			$this->em->getConnection()->commit();
		}
		catch (Exception $e)
		{
			$this->em->getConnection()->rollback();
			$this->em->close();
				
			throw $e;
		}
	}	
}