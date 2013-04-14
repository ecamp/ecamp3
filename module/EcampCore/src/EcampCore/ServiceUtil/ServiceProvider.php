<?php

namespace EcampCore\ServiceUtil;

use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceProvider 
{
	/** @var ServiceLocatorInterface */
	private $serviceLocator;
	
	private $internal;
	
	
	public function __construct(ServiceLocatorInterface $serviceLocator, $internal = false){
		$this->serviceLocator = $serviceLocator;
		$this->internal = $internal;
	}
	
		
	/**
	 * @return EcampCore\Service\AvatarService 
	 */
	public function avatarService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.avatar');
		} else {
			return $this->serviceLocator->get('ecamp.service.avatar');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\CampService 
	 */
	public function campService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.camp');
		} else {
			return $this->serviceLocator->get('ecamp.service.camp');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\ContributonService 
	 */
	public function contributonService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.contributon');
		} else {
			return $this->serviceLocator->get('ecamp.service.contributon');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\DayService 
	 */
	public function dayService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.day');
		} else {
			return $this->serviceLocator->get('ecamp.service.day');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\EventInstanceService 
	 */
	public function eventInstanceService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.eventinstance');
		} else {
			return $this->serviceLocator->get('ecamp.service.eventinstance');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\EventService 
	 */
	public function eventService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.event');
		} else {
			return $this->serviceLocator->get('ecamp.service.event');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\GroupService 
	 */
	public function groupService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.group');
		} else {
			return $this->serviceLocator->get('ecamp.service.group');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\LoginService 
	 */
	public function loginService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.login');
		} else {
			return $this->serviceLocator->get('ecamp.service.login');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\MembershipService 
	 */
	public function membershipService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.membership');
		} else {
			return $this->serviceLocator->get('ecamp.service.membership');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\PeriodService 
	 */
	public function periodService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.period');
		} else {
			return $this->serviceLocator->get('ecamp.service.period');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\RegisterService 
	 */
	public function registerService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.register');
		} else {
			return $this->serviceLocator->get('ecamp.service.register');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\RelationshipService 
	 */
	public function relationshipService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.relationship');
		} else {
			return $this->serviceLocator->get('ecamp.service.relationship');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\SearchUserService 
	 */
	public function searchUserService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.searchuser');
		} else {
			return $this->serviceLocator->get('ecamp.service.searchuser');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\SupportService 
	 */
	public function supportService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.support');
		} else {
			return $this->serviceLocator->get('ecamp.service.support');
		}
	}
	
	
	/**
	 * @return EcampCore\Service\UserService 
	 */
	public function userService(){
		if($this->internal){
			return $this->serviceLocator->get('ecamp.internal.service.user');
		} else {
			return $this->serviceLocator->get('ecamp.service.user');
		}
	}
	
}
	