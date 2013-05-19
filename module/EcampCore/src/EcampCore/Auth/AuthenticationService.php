<?php

namespace EcampCore\Auth;

use Zend\Authentication\Storage\StorageInterface;

use Zend\Authentication\Storage\Session;

class AuthenticationService 
	extends \Zend\Authentication\AuthenticationService
{
	
	const MEMBER_DEFAULT = 'origStorage';
	
	/**
	 * @var StorageInterface
	 */
	private $origStorage = null;
	
	/**
	 * @return StorageInterface
	 */
	public function getOrigStorage(){
		if(null === $this->origStorage){
			$this->setOrigStorage(
				new Session(Session::NAMESPACE_DEFAULT, self::MEMBER_DEFAULT));
		}
		
		return $this->origStorage;
	}
	
	/**
	 * @param StorageInterface $storage
	 */
	public function setOrigStorage(StorageInterface $storage){
		$this->origStorage = $storage;
		return $this;
	}
	
	
	public function replaceIdentity($identity){
		if($this->hasIdentity()){
			if($this->getOrigStorage()->isEmpty()){
				$this->getOrigStorage()->write($this->getIdentity());
			}
			
			$this->clearIdentity();
			$this->getStorage()->write($identity);
		}
	}
	
	
	public function restoreIdentity(){
		if(!$this->getOrigStorage()->isEmpty()){
			$this->getStorage()->write($this->getOrigStorage()->read());
			$this->getOrigStorage()->clear();
		}
	}
	
}
