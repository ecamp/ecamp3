<?php

namespace EcampCore\ServiceUtil;

use EcampCore\Service\ServiceBase;

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceWrapper 
	implements ResourceInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	private $serviceLocator;
	
	/**
	 * @var EcampCore\Acl\DefaultAcl
	 */
	private $acl;
	
	/**
	 * The protected Resource
	 * @var EcampCore\Service\ServiceBase
	 */
	private $service = null;
	
	/**
	 * @var ValidationException
	 */
	private static $validationException = null;
	
// 	private $transaction = null;
	
	public static $simulated = false;
	
	public function __construct(
		ServiceLocatorInterface $serviceLocator,
		ServiceBase $service
	){
		$this->serviceLocator = $serviceLocator;
		$this->service = $service;
	}
	
	
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEm(){
		return $this->serviceLocator->get('doctrine.entitymanager.orm_default');
	}
	
	/**
	 * @return EcampCore\Acl\ContextProvider
	 */
	public function getContextProvider(){
		return $this->serviceLocator->get('ecampcore.acl.contextprovider');
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
	
	public function getResourceId(){
		return $this->service->getResourceId();
	}
	
	public function __call($method, $args)
	{
		if( !method_exists($this->service, $method) ){
			throw new \Exception("Method $method does not exist.");
		}
		
		
		$acl = $this->serviceLocator->get('ecampcore.internal.acl');
		if(!$acl->hasResource($this->service)){			
    		$acl->addResource($this->service);
		}
   		$this->service->_setupAcl();
		
		
		if(!$this->isAllowed($method)){
			throw new \Exception("No Access on " . $this->service->getResourceId() . "::" . $method);
		}
		
		$this->start();
		
		$r = call_user_func_array(array($this->service, $method), $args);
		
		$this->end();
		
		return $r;
	}
	 
	public function Simulate(){
		return new ServiceSimulator($this);
	}
	
	private function start()
	{
		self::$validationException = null;
		
		$uow = $this->getEm()->getUnitOfWork();
		$uow->computeChangeSets();
		
		$upd = $uow->getScheduledEntityUpdates();
		$ins = $uow->getScheduledEntityInsertions();
		$del = $uow->getScheduledEntityDeletions();
		$colupd = $uow->getScheduledCollectionUpdates();
		$coldel = $uow->getScheduledCollectionDeletions();
		
		if( !empty($upd) || !empty($ins)|| !empty($del)|| !empty($colupd)|| !empty($coldel) )
			throw new \Exception("You tried to edit an entity outside the service layer.");
		
		$this->getEm()->getConnection()->beginTransaction();
	}
	
	private function end()
	{
		$this->flushAndCommit();
		
		if(isset(self::$validationException)){
			throw self::$validationException;
		}
	}
	
	private function isAllowed($privilege = NULL){
		$acl = $this->serviceLocator->get('ecampcore.internal.acl');
		$roles = $acl->getRolesInContext();
		
		foreach ($roles as $role){
			if($acl->isAllowed($role, $this->service, $privilege)){
				return true;
			}
		}
		
		if($this->getContextProvider()->getMe() == null){
			// Ask for Auth
			// Authentification-Header
			// OR GoTo-Login
			die("Not Authenticated");
			// 401??
		}
		
		return false;
		// 403??
	}
	
	private function flushAndCommit(){
		
		if(self::hasFailed() || self::isSimulated()){
			$this->getEm()->getConnection()->rollback();
			
			if( self::isSimulated() ){	
				/** TODO: this call is problematic, find a better solution */
				$this->getEm()->clear();
			}
			return;
		}
	
		try{
			$this->getEm()->flush();
			$this->getEm()->getConnection()->commit();
		}
		catch (Exception $e){
			$this->getEm()->getConnection()->rollback();
			$this->getEm()->close();
				
			throw $e;
		}
	}	
}
