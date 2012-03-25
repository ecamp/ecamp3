<?php

namespace Core\Service;

class ValidationWrapper
	implements \Zend_Acl_Resource_Interface
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var ValidationException
	 */
	private static $validationException = null;
	
	private static $serviceNestingLevel = 0;
	
	private static $transaction = null;
	
	
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
	
	public static function getServiceNestingLevel()
	{
		return self::$serviceNestingLevel;
	}
	
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	private $kernel;
	
	private $service = null;
		
	public function __construct(\Zend_Acl_Resource_Interface $service)
	{
		$this->service = $service;
	}
	
	public function postInject()
	{
		$this->kernel->Inject($this->service);
		unset($this->kernel);
	}
	
	public function getResourceId()
	{
		return $this->service->getResourceId();
	}
	
	
	public function __call($method, $args)
	{
		$this->start();
		
		$r = call_user_func_array(array($this->service, $method), $args);
		
		$this->end();
		
		return $r;
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
			if(isset(self::$validationException))
			{
				throw self::$validationException;
			}
			
			$this->flushAndCommit();
		}
	}
	
	private function flushAndCommit()
	{
		if(self::hasFailed() )
		{
			$this->rollback();
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