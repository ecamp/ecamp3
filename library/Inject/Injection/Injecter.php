<?php

namespace Inject\Injection;



class Injecter
{

	/**
	 * @var \Inject\Kernel
	 */
	private $kernel;


	public function __construct(\Inject\Kernel $kernel)
	{
		$this->kernel = $kernel;
	}


	public function InjectDependencies($object)
	{
		$reflectionClass = new \Zend_Reflection_Class($object);

		//$reflectionProperty = new \Zend_Reflection_Property(null, null);

		foreach($reflectionClass->getProperties() as $reflectionProperty)
		{
			/** @var \Zend_Reflection_Property $reflectionProperty */
			$reflectionDocComment = $reflectionProperty->getDocComment();

			if($reflectionDocComment)
			{
				if($reflectionDocComment->hasTag("Inject"))
				{
					$reflectionDocCommentTag = $reflectionDocComment->getTag("Inject");

					$dependencyName = $reflectionDocCommentTag->getDescription();
					$dependencyName = trim($dependencyName);

					$dependency = $this->kernel->Get($dependencyName);

					$reflectionProperty->setAccessible(true);
					$reflectionProperty->setValue($object, $dependency);
				}
			}

		}

		//print_r($reflectionClass);
	}
}
