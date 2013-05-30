<?php

namespace EcampCore\RepositoryTraits;

trait EventPrototypeRepositoryTrait
{
	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	private $eventPrototypeRepository;
	
	/**
	 * @return Doctrine\ORM\EntityRepository
	 */
	public function getEventPrototypeRepository(){
		return $this->eventPrototypeRepository;
	}
	
	public function setEventPrototypeRepository($eventPrototypeRepository){
		$this->eventPrototypeRepository = $eventPrototypeRepository;
		return $this;
	}
}
