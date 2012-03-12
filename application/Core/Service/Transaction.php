<?php

namespace Api\Service;

class Transaction
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var Doctrine\DBAL\Connection
	 */
	private $conn;
	
	
	private $isBaseTransaction = false;
	
	
	public function __construct($em)
	{
		$this->em = $em;
		$this->conn = $this->em->getConnection();
	}
	
	
	public function beginTransaction()
	{		
		if($this->conn->getTransactionNestingLevel() == 0)
		{
			$this->conn->beginTransaction();
			$this->isBaseTransaction = true;
		}
	}
	
	
	public function commit($s)
	{
		if(! $this->isBaseTransaction)
		{	return;	}
		
		if($s)
		{
			$this->rollback();
			return;
		}
		
		try
		{
			$this->conn->commit();
		}
		catch (\PDOException $e)
		{
			$this->rollback();
			$this->close();
			
			throw $e;
		}
	}
	
	
	public function rollback()
	{
		if($this->isBaseTransaction)
		{	$this->conn->rollback();	}
	}
	
	
	protected function close()
	{
		$this->em->close();
	}
	
}