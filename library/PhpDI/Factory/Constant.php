<?php

namespace PhpDI\Factory;

class Constant implements IFactory
{
	private $const = null;
	
	/**
	 * Constructor for ConstantProvider
	 * @param $const|NULL
	 */
	public function __construct($const = NULL)
	{
		$this->const = $const;
	}
	
	/**
	 * Set constant for ConstantProvider
	 * @param $const
	 */
	public function setConst($const)
	{
		$this->const = $const;
	}
	
	/**
	 * Returns the constant of the ConstantProvider
	 * @return object
	 */
	public function getConst()
	{
		return $this->const;
	}
	
	/**
	 * Provides the constant of the ConstantProvider
	 * @return object
	 */
	public function create()
	{
		return $this->const;
	}
	
}