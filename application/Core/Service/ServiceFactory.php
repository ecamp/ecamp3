<?php

namespace Core\Service;

class ServiceFactory
	implements \PhpDI\Factory\IFactory
{
	
	/**
	 * @var string
	 */
	private $service;
	
	
	public function __construct($service)
	{
		$this->service = $service;
	}
	
	public function create()
	{
		$serviceFactory = new \PhpDI\Factory\Constructor($this->service);
		$validationWrapper = new ValidationWrapper($serviceFactory->create());
		$acWrapperFactory = new \Core\Acl\ACWrapperFactory($validationWrapper);
		
		return $acWrapperFactory->create();
	}
	
}