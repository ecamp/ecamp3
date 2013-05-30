<?php

namespace EcampCore\RepositoryTraits;

trait UIdRepositoryTrait
{
	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	private $uIdRepository;
	
	/**
	 * @return Doctrine\ORM\EntityRepository
	 */
	public function getUIdRepository(){
		return $this->uIdRepository;
	}
	
	public function setUIdRepository($uIdRepository){
		$this->uIdRepository = $uIdRepository;
		return $this;
	}
}
