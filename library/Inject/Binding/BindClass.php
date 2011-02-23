<?php

namespace Inject\Binding;

use \Inject\Binding\IBinding;
use \Inject\Syntax\IAsSingleton;

class BindClass
	implements IBinding, IAsSingleton
{

	/**
	 * @var bool
	 */
	private $isSingleton;

	/**
	 * @var string
	 */
	private $classname;

	/**
	 * @var object
	 */
	private $instance;


	public function __construct($classname)
	{
		$this->isSingleton = false;
		
		$this->classname = $classname;
	}


	/**
	 * @return object
	 */
	public function Get()
	{
		if(!$this->isSingleton || is_null($this->instance))
				{
			$this->instance = new $this->classname();
		}

		return $this->instance;
	}


	/**
	 * @return string
	 */
	public function ClassName()
	{
		return $this->classname;
	}


	/**
	 * @return IAsSingleton
	 */
	public function AsSingleton()
	{
		$this->isSingleton = true;

		return $this;
	}


	/**
	 * @return IAsSingleton
	 */
	public function GetAlwaysNewInstance()
	{
		$this->isSingleton = false;

		return $this;
	}
}
