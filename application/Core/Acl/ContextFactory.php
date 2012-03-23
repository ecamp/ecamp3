<?php

namespace Core\Acl;


use PhpDI\Factory\IFactory;

class ContextFactory implements IFactory
{
	
	public function create()
	{
		$contextStorage = \Zend_Registry::get('kernel')->Get("Core\Acl\ContextStorage");
		
		return $contextStorage->getContext();
	}
	
}