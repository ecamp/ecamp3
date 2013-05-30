<?php

namespace EcampCore\RepositoryTraits;

trait GroupRequestRepositoryTrait
{
	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	private $groupRequestRepository;
	
	/**
	 * @return Doctrine\ORM\EntityRepository
	 */
	public function getGroupRequestRepository(){
		return $this->groupRequestRepository;
	}
	
	public function setGroupRequestRepository($groupRequestRepository){
		$this->groupRequestRepository = $groupRequestRepository;
		return $this;
	}
}
