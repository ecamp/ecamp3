<?php

namespace EcampCore\RepositoryTraits;

trait EventRepositoryTrait
{
	/**
	 * @var EcampCore\Repository\EventRepository
	 */
	private $eventRepository;
	
	/**
	 * @return EcampCore\Repository\EventRepository
	 */
	public function getEventRepository(){
		return $this->eventRepository;
	}
	
	public function setEventRepository($eventRepository){
		$this->eventRepository = $eventRepository;
		return $this;
	}
}
