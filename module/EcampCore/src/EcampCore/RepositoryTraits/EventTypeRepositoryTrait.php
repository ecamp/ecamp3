<?php

namespace EcampCore\RepositoryTraits;

trait EventTypeRepositoryTrait
{
	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	private $eventTypeRepository;
	
	/**
	 * @return Doctrine\ORM\EntityRepository
	 */
	public function getEventTypeRepository(){
		return $this->eventTypeRepository;
	}
	
	public function setEventTypeRepository($eventTypeRepository){
		$this->eventTypeRepository = $eventTypeRepository;
		return $this;
	}
}
