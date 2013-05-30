<?php

namespace EcampCore\RepositoryTraits;

trait DayRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\DayRepository
	 */
	private $dayRepository;
	
	/**
	 * @return EcampCore\Repository\DayRepository
	 */
	public function getDayRepository(){
		return $this->dayRepository;
	}
	
	public function setDayRepository($dayRepository){
		$this->dayRepository = $dayRepository;
		return $this;
	}
}
