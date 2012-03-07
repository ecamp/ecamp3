<?php

namespace Inject\Factory;

use Inject\Syntax\IFactory;

class ByClassname
	implements IFactory
{
	
	/**
	 * The classname of the type of object, which is created
	 * 
	 * @var string
	 */
	private $classname;
	
	
	public function __construct($classname)
	{
		$this->classname = $classname;
	}
	
	public function Create()
	{
		return new $this->classname();
	}
}