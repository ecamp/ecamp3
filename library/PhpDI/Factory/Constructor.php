<?php

namespace PhpDI\Factory;

class Constructor implements IFactory
{
	private $classname = null;
	
	public function __construct($classname = null)
	{
		if(!class_exists($classname))
		{
			throw new \Exception("Invalid Classname; Class $classname does not exist");
		}
		
		$this->classname = $classname;
	}
	
	public function setClassname($classname)
	{
		if(!class_exists($classname))
		{
			throw new \Exception("Invalid Classname; Class $classname does not exist");
		}
		
		$this->classname = $classname; 
	}
	
	public function getClassname()
	{
		return $this->classname;
	}
	
	public function create()
	{
		$classname = $this->classname;
		
		return new $classname();
	}
	
}