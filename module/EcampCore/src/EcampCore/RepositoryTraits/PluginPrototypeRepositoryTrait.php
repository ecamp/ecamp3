<?php

namespace EcampCore\RepositoryTraits;

trait PluginPrototypeRepositoryTrait
{
	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	private $pluginPrototypeRepository;
	
	/**
	 * @return Doctrine\ORM\EntityRepository
	 */
	public function getPluginPrototypeRepository(){
		return $this->pluginPrototypeRepository;
	}
	
	public function setPluginPrototypeRepository($pluginPrototypeRepository){
		$this->pluginPrototypeRepository = $pluginPrototypeRepository;
		return $this;
	}
}
