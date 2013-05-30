<?php

namespace EcampCore\RepositoryTraits;

trait PeriodRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\PeriodRepository
	 */
	private $periodRepository;
	
	/**
	 * @return EcampCore\Repository\PeriodRepository
	 */
	public function getPeriodRepository(){
		return $this->periodRepository;
	}
	
	public function setPeriodRepository($periodRepository){
		$this->periodRepository = $periodRepository;
		return $this;
	}
}
