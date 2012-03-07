<?php

namespace PhpDI\Dependency;

use PhpDI\IKernel;
use PhpDI\Dependency\Placeholder;
use PhpDI\Injector\PropertyInjector;


class Scan
{
	private $kernel = null;
	
	private $reflectionClass = null;
	
	private $dependencies = array();
	
	
	public function __construct(IKernel $kernel, \Zend_Reflection_Class $reflectionClass)
	{
		$this->kernel = $kernel;
		$this->reflectionClass = $reflectionClass;
		$this->scan();
	}
	
	
	public function scan()
	{
		$this->dependencies = array();
		
		foreach($this->reflectionClass->getProperties() as $reflectionProp)
		{
			/** @var \Zend_Reflection_Property $reflectionProperty */
			$reflectionDocComment = $reflectionProp->getDocComment();
		
			if($reflectionDocComment && $reflectionDocComment->hasTag("Inject"))
			{
				$reflectionDocCommentTag = $reflectionDocComment->getTag("Inject");
	
				$dependencyName = $reflectionDocCommentTag->getDescription();
				$dependencyName = trim($dependencyName);
				
				$this->dependencies[$reflectionProp->getName()] = array(
					'property' => $reflectionProp->getName(), 
					'dependency' => $dependencyName);
			}
		}
	}
	
	
	public function createPlaceholders($object)
	{
		// TODO: Check whether $object fits for $this->reflectionClass
		//if(!is_a($object, $this->reflectionClass->getName()))
		
		if(method_exists($object, 'preInject'))
		{	$object->preInject();	}
		
		foreach($this->dependencies as $dependency)
		{
			$propertyName = $dependency['property'];
			$dependencyName = $dependency['dependency'];
			
			$dependencyInjector = new PropertyInjector($object, $propertyName);
			$placeholder = new Placeholder($this->kernel, $dependencyName, $dependencyInjector);
			
			$dependencyInjector->inject($placeholder);
		}
		
		if(method_exists($object, 'postInject'))
		{	$object->postInject();	}
	}
}