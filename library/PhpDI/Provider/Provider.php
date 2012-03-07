<?php

namespace PhpDI\Provider;

use PhpDI\IKernel;
use PhpDI\Dependency\Scan;
use PhpDI\Factory\IFactory;


class Provider implements IProvider
{
	
	/** @var PhpDI\IKernel */
	private $kernel = null;
	
	
	/** @var PhpDI\Factory\IFactory */
	private $factory = null;
	
	
	/** @var PhpDI\Dependency\Scan */
	private $scan = null;
	
	
	private $asSingleton = false;
	
	private $instance = null;
	
	
	
	public function __construct(IFactory $factory, IKernel $kernel)
	{
		$this->factory = $factory;
		$this->kernel = $kernel;
	}
	
	public function provide()
	{
		if(!$this->asSingleton || is_null($this->instance))
		{
			$this->instance = $this->factory->create();
			
			if(! is_object($this->instance))
			{
				return $this->instance;
			}
				
			if(is_null($this->scan))
			{
				$reflectionClass = new \Zend_Reflection_Class($this->instance);
				$this->scan = new Scan($this->kernel, $reflectionClass);
			}
		
			$this->scan->createPlaceholders($this->instance);
		}
		
		return $this->instance;
	}
	
	
	/**
	 * @param boolean $asSingleton
	 * @return PhpDI\Provider
	 */
	public function asSingleton($asSingleton = true)
	{
		$this->asSingleton = $asSingleton;
		
		return $this;
	}
	
}