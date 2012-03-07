<?php

namespace Core\Acl;

use PhpDI\Factory\Constructor;

use PhpDI\Factory\Constant;

use PhpDI\Factory\IFactory;
use PhpDI\Provider\IProvider;

class ACWrapperFactory implements IFactory
{
	private $factory;
	
	public function __construct($resourceId)
	{
		if(is_object($resourceId))
		{
			if($resourceId instanceof IFactory)
			{	$this->factory = $resourceId;	}
			else
			{	$this->factory = new Constant($resourceId);	}
			
			return;
		}
		
		if(is_string($resourceId))
		{
			$this->factory = new Constructor($resourceId);
			
			return;
		}
	}
	
	public function create()
	{
		return new ACWrapper($this->factory->create());
	}
	
}