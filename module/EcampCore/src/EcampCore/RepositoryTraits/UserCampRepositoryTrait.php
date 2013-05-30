<?php

namespace EcampCore\RepositoryTraits;

trait UserCampRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\ContributorRepository
	 */
	private $userCampRepository;
	
	/**
	 * @return EcampCore\Repository\ContributorRepository
	 */
	public function getUserCampRepository(){
		return $this->userCampRepository;
	}
	
	public function setUserCampRepository($userCampRepository){
		$this->userCampRepository = $userCampRepository;
		return $this;
	}
}
