<?php

namespace Inject;

use Inject\Syntax\IFactory;

class DependencyContainer
{
	
	/** @var IFactory */
	private $factory = null;
	
	private $classname = null;
	
	private $isSingleton = false;
	
	private $holdsDependencies = false;
	
	private $instance = null;
	
	
	public function __construct($classname, IFactory $factory)
	{
		$this->classname = $classname;
		$this->factory = $factory;
	}
	
	/**
	* @return object
	*/
	public function Get()
	{
		if(!$this->isSingleton || is_null($this->instance))
		{
			$this->instance = $this->factory->Create();
			$this->holdsDependencies = false;
		}
	
		return $this->instance;
	}
	
	/**
	 * @return string
	 */
	public function ClassName()
	{
		return $this->classname;
	}
	
	/**
	 * @param bool $asSingleton
	 * @return DependencyContainer
	 */
	public function AsSingleton($asSingleton = true)
	{
		$this->isSingleton = $asSingleton;
		
		return $this;
	}
	
	
	public function IsDependencyInjectionRequired()
	{
		return ! $this->holdsDependencies;
	}
	
	public function DependenciesInjected()
	{
		$this->holdsDependencies = true;
	}
}