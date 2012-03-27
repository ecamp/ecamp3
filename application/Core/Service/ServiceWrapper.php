<?php

namespace Core\Service;

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
	
	private static $serviceNestingLevel = 0;
	
	private static $transaction = null;
	
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
	
	public static function getServiceNestingLevel()
	{
		return self::$serviceNestingLevel;
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
		
		if( ServiceWrapper::getServiceNestingLevel() == 0 && ! $this->isAllowed($method) )
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
	
	private function start()
	{
		if(self::$serviceNestingLevel == 0)
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
	
		self::$serviceNestingLevel++;
	}
	
	private function end()
	{
		self::$serviceNestingLevel--;
	
		if( self::$serviceNestingLevel == 0 )
		{
			$this->flushAndCommit();
			
			if(isset(self::$validationException))
			{
				throw self::$validationException;
			}
		}
	}
	
	private function isAllowed($privilege = NULL)
	{
		// TODO: Load current roles from $acl or form some AUTH mechanism.
		$roles = $this->acl->getRolesInContext();
	
	
		// TODO: Remove default return value
		// FOR DEVELOPING:
		//return true;
	
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
			$this->em->rollback();
			
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