<?php

namespace EcampLib\Service;

class ServiceSimulator
{
	/**
	 * @var ServiceWrapper
	 */
	private $wrapper;
	
	public function __construct(ServiceWrapper $wrapper){
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