<?php

namespace PhpDI;


class Config implements \ArrayAccess
{
	private $providers = array();
	
	
	public function offsetExists ($classname)
	{
		return isset($this->providers[$classname]);
	}
	
	public function offsetGet ($classname)
	{
		return $this->providers[$classname];
	}
	
	public function offsetSet ($classname, $provider)
	{
		$this->providers[$classname] = $provider;
	}
	
	public function offsetUnset ($classname)
	{
		unset($this->providers[$classname]);
	}
	
	
	// TODO: SetDebugMode($debug = true)
	// TODO: IsDebugMode()
}