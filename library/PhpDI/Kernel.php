<?php

namespace PhpDI;

use PhpDI\Syntax\Bind;
use PhpDI\Syntax\Rebind;
use PhpDI\Dependency\Scan;


class Kernel implements IKernel
{
	private $config = null;
	
	
	public function __construct()
	{
		$this->config = new Config();
	}
	
	/**
	 * @param string $classname
	 */
	public function Get($classname)
	{
		if(isset($this->config[$classname]))
		{	return $this->config[$classname]->provide();	}
		
		if(class_exists($classname))
		{
			$instance = new $classname();
			$this->Inject($instance);
			
			return $instance;
		}
		
		throw new \Exception("Kernel can not handle request for ($classname)");
	}
	
	
	/**
	 * @param object $object
	 */
	public function Inject($instance)
	{
		if(! is_object($instance))
		{	throw new \Exception("Injection can only be applied to Objects");	}
		
		$reflectionClass = new \Zend_Reflection_Class($instance);
		$scan = new Scan($this, $reflectionClass);
		
		$scan->createPlaceholders($instance);
	}
	
	/**
	 * @param string $classname
	 * @return PhpDI\Syntax\Bind
	 */
	public function Bind($classname)
	{
		if(isset($this->config[$classname]))
		{	throw new \Exception("Use Rebind to change Binding");	}
		
		return new Bind($this, $this->config, $classname);
	}
	
	
	/**
	 * @param string $classname
	 * @return PhpDI\Syntax\Rebind
	 */
	public function Rebind($classname)
	{
		if(! isset($this->config[$classname]))
		{	throw new \Exception("Use Bind for initial Binding");	}
		
		return new Rebind($this, $this->config, $classname);
	}
	
	
	/**
	 * @param string $classname
	 */
	public function Unbind($classname)
	{
		unset($this->config[$classname]);
	}
	
}

