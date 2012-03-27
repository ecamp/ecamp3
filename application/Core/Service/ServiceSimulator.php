<?php

namespace Core\Service;

class ServiceSimulator
{
	private $wrapper;
	
	public function __construct($wrapper)
	{
		$this->wrapper = $wrapper;
	}
	
	public function __call($method, $args)
	{	
		ServiceWrapper::$simulated = true;
		
		$ret = $this->wrapper->__call($method, $args);
	
		ServiceWrapper::$simulated = false;
		
		return $ret;
	}
}