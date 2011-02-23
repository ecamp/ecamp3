<?php

namespace Inject\Binding;

use \Inject\Binding\IBinding;

class BindConstant
	implements IBinding
{

	/**
	 * @var string
	 */
	private $classname;

	/**
	 * @var object
	 */
	private $const;


	public function __construct($classname, $const)
	{
		$this->classname = $classname;
		$this->const = $const;
	}


	public function Get()
	{
		return $this->const;
	}


	/**
	 * @return string
	 */
	public function ClassName()
	{
		return $this->classname;
	}
}
