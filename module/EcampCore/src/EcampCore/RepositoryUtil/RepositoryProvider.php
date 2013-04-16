<?php

namespace EcampCore\RepositoryUtil;

use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryProvider 
{
	/** @var ServiceLocatorInterface */
	private $serviceLocator;
	
	
	public function __construct(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
		
	/**
	 * @return EcampCore\Repository\CampRepository 
	 */
	public function campRepository(){
		return $this->serviceLocator->get('ecamp.repo.camp');
	}
	
	
	/**
	 * @return EcampCore\Repository\ContributorRepository 
	 */
	public function contributorRepository(){
		return $this->serviceLocator->get('ecamp.repo.contributor');
	}
	
	
	/**
	 * @return EcampCore\Repository\DayRepository 
	 */
	public function dayRepository(){
		return $this->serviceLocator->get('ecamp.repo.day');
	}
	
	
	/**
	 * @return EcampCore\Repository\EventInstanceRepository 
	 */
	public function eventInstanceRepository(){
		return $this->serviceLocator->get('ecamp.repo.eventinstance');
	}
	
	
	/**
	 * @return EcampCore\Repository\EventRepository 
	 */
	public function eventRepository(){
		return $this->serviceLocator->get('ecamp.repo.event');
	}
	
	
	/**
	 * @return EcampCore\Repository\EventRespRepository 
	 */
	public function eventRespRepository(){
		return $this->serviceLocator->get('ecamp.repo.eventresp');
	}
	
	
	/**
	 * @return EcampCore\Repository\GroupRepository 
	 */
	public function groupRepository(){
		return $this->serviceLocator->get('ecamp.repo.group');
	}
	
	
	/**
	 * @return EcampCore\Repository\LoginRepository 
	 */
	public function loginRepository(){
		return $this->serviceLocator->get('ecamp.repo.login');
	}
	
	
	/**
	 * @return EcampCore\Repository\PeriodRepository 
	 */
	public function periodRepository(){
		return $this->serviceLocator->get('ecamp.repo.period');
	}
	
	
	/**
	 * @return EcampCore\Repository\UserGroupRepository 
	 */
	public function userGroupRepository(){
		return $this->serviceLocator->get('ecamp.repo.usergroup');
	}
	
	
	/**
	 * @return EcampCore\Repository\UserRelationshipRepository 
	 */
	public function userRelationshipRepository(){
		return $this->serviceLocator->get('ecamp.repo.userrelationship');
	}
	
	
	/**
	 * @return EcampCore\Repository\UserRepository 
	 */
	public function userRepository(){
		return $this->serviceLocator->get('ecamp.repo.user');
	}
	
}
	