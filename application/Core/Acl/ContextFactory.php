<?php

namespace Core\Acl;


use PhpDI\Factory\IFactory;

class ContextFactory implements IFactory
{
	
	private $readonly;
	
	
	public function __construct($readonly = true)
	{
		$this->readonly = $readonly;
	}
	
	
	public function create()
	{
		$contextStorage = \Zend_Registry::get('kernel')->Get("Core\Acl\ContextStorage");
		
		if($this->readonly === false)
		{	return $contextStorage->getContext();	}
		
		return $contextStorage->getReadonlyContext();
	}
	
}