<?php

namespace PhpDI\Dependency;

use PhpDI\IKernel;
use PhpDI\Injector\IInjector;


class Placeholder implements \Iterator, \ArrayAccess, \Serializable
{
	/** @var PhpDI\IKernel */
	private $kernel = null;
	
	/** @var string */
	private $dependencyName = null;
	
	/** @var PhpDI\Injection\Injector\IInjector */
	private $dependencyInjector = null;
		
	
	public function __construct(
		IKernel $kernel, $dependencyName,
		IInjector $dependencyInjector)
	{
		$this->kernel = $kernel;
		$this->dependencyName = $dependencyName; 
		$this->dependencyInjector = $dependencyInjector;
	}
	
	private function inject()
	{
		$dependency = $this->kernel->Get($this->dependencyName);
		
		if(! is_null($this->dependencyInjector))
		{
			$this->dependencyInjector->inject($dependency);
			$this->dependencyInjector = null;
		}
		
		return $dependency;
	}
	
	
	
	/** GET :: SET :: UNSET :: ISSET **/
	
	public function __get($property)
	{
		$dependency = $this->inject();
		return $dependency->{$property};
	}
	
	public function __set($property, $value)
	{
		$dependency = $this->inject();
		$dependency->{$property} = $value;
	}
	
	public function __unset($property)
	{
		$dependency = $this->inject();
		unset($dependency->{$property});
	}
	
	public function __isset($property)
	{
		$dependency = $this->inject();
		return isset($dependency->{$property});
	}
	
	
	
	/** CALL :: INVOKE :: TOSTRING **/
	
	public function __call($method, $args)
	{
		$dependency = $this->inject();
		return call_user_func_array(array($dependency, $method), $args);
	}
	
	public function __invoke()
	{
		$dependency = $this->inject();
		return call_user_func_array($dependency, func_get_args());	
	}
	
	public function __toString()
	{
		$dependency = $this->inject();
		return (string) $dependency;
	}
	
	
	
	/** ITERATOR **/
	
	public function current()
	{
		$dependency = $this->inject();
		return current($dependency);
	}
	
	public function key()
	{
		$dependency = $this->inject();
		return key($dependency);
	}
	
	public function next()
	{
		$dependency = $this->inject();
		next($dependency);
	}
	
	public function rewind()
	{
		$dependency = $this->inject();
		rewind($dependency);
	}
	
	public function valid()
	{
		$dependency = $this->inject();
		return (current($dependency) !== FALSE);
	}
	
	
	
	/** ARRAY ACCESS **/
	
	public function offsetExists ($classname)
	{
		$dependency = $this->inject();
		return isset($dependency[$classname]);
	}
	
	public function offsetGet ($classname)
	{
		$dependency = $this->inject();
		return $dependency[$classname];
	}
	
	public function offsetSet ($classname, $provider)
	{
		$dependency = $this->inject();
		$dependency[$classname] = $provider;
	}
	
	public function offsetUnset ($classname)
	{
		$dependency = $this->inject();
		unset($dependency[$classname]);
	}
	
	
	
	/** SERIALIZABLE **/
	
	public function serialize()
	{
		$dependency = $this->inject();
		return serialize($dependency);
	}
	
	public function unserialize($data)
	{
		throw new \Exception("Not implemented");
	}
	
}
