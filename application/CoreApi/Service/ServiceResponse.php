<?php

namespace CoreApi\Service;

class ServiceResponse
{
	const SUCCESS = 'valid';
	const VALIDATION_FAILED = 'validation_failed'; 
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	private $isSim;
	
	private $state = self::SUCCESS;
	
	
	private $returnValue = null;
	
	private $messages = array();
	
	
	
	public function __construct($em, $s)
	{
		$this->em = $em;
		$this->isSim = $s;
	}
	
	
	public function beginTransaction()
	{
		$this->em->getConnection()->beginTransaction();
		
		return $this;
	}
	
	public function flushAndCommit()
	{
		if($this->isSim || $this->state != self::SUCCESS)
		{
			$this->rollback();
		}
		else
		{
			try
			{
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
		
		return $this;
	}
	
	public  function rollback()
	{
		$this->em->getConnection()->rollback();
		
		return $this;
	}
	
	private function rollbackAll()
	{
		for($i = $this->em->getConnection()->getTransactionNestingLevel(); $i>0; $i--)
		{	$this->em->getConnection()->rollback();	}
	}
	
	
	
	public function isSimulated()
	{
		return $this->isSim;
	}
	
	public function validationFailed($bool = true)
	{
		if($bool)
			$this->success = self::VALIDATION_FAILED;
		
		return $bool;
	}
	
	public function process(ServiceResponse $resp)
	{
		$this->success &= $resp->success;
		$this->messages = array_merge($this->messages, $resp->messages);
		
		$this->returnValue = $returnValue->getReturn();
	}
	
	
	public function __invoke($returnValue)
	{
		if($returnValue instanceof ServiceResponse)
		{	$this->process($returnValue);	}
		else
		{	$this->setReturn($returnValue);	}
		
		return $this;
	}
	
	public function setReturn($returnValue)
	{
		if($returnValue instanceof \Core\Entity\BaseEntity)
		{	$returnValue = $returnValue->asReadonly();	}
			
		$this->returnValue = $returnValue;
	}
	
	public function getReturn()
	{
		return $this->returnValue;
	}
	
	
	
	public function addMessage($message)
	{
		$this->messages[] = $message;
		
		return $this;
	}
	
	public function getMessages()
	{
		return $this->messages;
	}
}