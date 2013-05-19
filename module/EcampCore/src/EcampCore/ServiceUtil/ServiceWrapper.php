<?php

namespace EcampCore\ServiceUtil;

use EcampCore\Service\ServiceBase;

use Doctrine\ORM\EntityManager;

class ServiceWrapper 
{
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * The protected Resource
	 * @var EcampCore\Service\ServiceBase
	 */
	private $service = null;
	
	/**
	 * @var ValidationException
	 */
	private static $validationException = null;
	
	
	public static $simulated = false;
	
	public function __construct(
		EntityManager $em,
		ServiceBase $service
	){
		$this->em = $em;
		$this->service = $service;
	}
	
	
	public static function validationFailed(){
		if(self::$validationException == null){
			self::$validationException = new ValidationException("ValidationException");
		}
	}
	
	public static function addValidationMessage($message){
		self::validationFailed();
		self::$validationException->addMessage($message);
	}
	
	public static function hasFailed(){
		return self::$validationException != null;
	}
	
	public static function isSimulated(){
		return self::$simulated;
	}
	
	public function __call($method, $args){
		
		if( !method_exists($this->service, $method) ){
			throw new \Exception("Method $method does not exist.");
		}
		
		$this->start();
   		
   		try{
   			
			$r = call_user_func_array(array($this->service, $method), $args);
				
   		}catch(AuthenticationRequiredException $ex){
			
   			$this->em->getConnection()->rollback();
   			// GoTo Authentication
			
		}catch (NoAccessException $ex){
   			
   			$this->em->getConnection()->rollback();
   			throw $ex;
   		}
   		
   		$this->end();
   		
		return $r;
	}
	
	
	public function Simulate(){
		return new ServiceSimulator($this);
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
		
		if( !empty($upd) || !empty($ins)|| !empty($del)|| !empty($colupd)|| !empty($coldel) ){
			throw new \Exception("You tried to edit an entity outside the service layer.");
		}
		
		$this->em->getConnection()->beginTransaction();
	}
	
	private function end()
	{
		$this->flushAndCommit();
		
		if(isset(self::$validationException)){
			throw self::$validationException;
		}
	}
	
	private function flushAndCommit(){
		
		if(self::hasFailed() || self::isSimulated()){
			$this->em->getConnection()->rollback();
			
			if( self::isSimulated() ){	
				/** TODO: this call is problematic, find a better solution */
				$this->em->clear();
			}
			return;
		}
	
		try{
			$this->em->flush();
			$this->em->getConnection()->commit();
		}
		catch (Exception $e){
			$this->em->getConnection()->rollback();
			$this->em->close();
				
			throw $e;
		}
	}	
}
