<?php

namespace PhpDI\Injector;

class PropertyInjector implements IInjector
{
	/** @var onject **/
	private $object;
	
	
	/** @var ReflectionProperty **/
	private $reflectionProperty;
	
	
	public function __construct($object, $property)
	{
		$reflectionClass = new \ReflectionClass($object);
		
		$this->object = $object;
		$this->reflectionProperty = $reflectionClass->getProperty($property);
	}
	
	
	public function inject($value)
	{
		$this->reflectionProperty->setAccessible(true);
		$this->reflectionProperty->setValue($this->object, $value);
	}
	
}
