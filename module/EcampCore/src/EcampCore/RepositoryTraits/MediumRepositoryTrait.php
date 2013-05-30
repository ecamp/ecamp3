<?php

namespace EcampCore\RepositoryTraits;

trait MediumRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\MediumRepository
	 */
	private $mediumRepository;
	
	/**
	 * @return EcampCore\Repository\MediumRepository
	 */
	public function getMediumRepository(){
		return $this->mediumRepository;
	}
	
	public function setMediumRepository($mediumRepository){
		$this->mediumRepository = $mediumRepository;
		return $this;
	}
}
