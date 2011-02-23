<?php

namespace Inject\Binding;

use \Inject\Syntax\IProvider;
use \Inject\Syntax\IAsSingleton;

class BindToProvider
	implements IBinding, IAsSingleton
{

	/**
	 * @var string
	 */
	private $classname;

	/**
	 * @var IProvider
	 */
	private $provider;

	/**
	 * @var bool
	 */
	private $isSingleton;

	/**
	 * @var object
	 */
	private $instance;


	/**
	 * @param \Syntax\IProvider $provider
	 */
	public function __construct($classname, IProvider $provider)
	{
		$this->isSingleton = false;

		$this->classname = $classname;
		$this->provider = $provider;


	}

	/**
	 * @return object
	 */
	public function Get()
	{
		if(!$this->isSingleton || is_null($this->instance))
		{
			$this->instance = $this->provider->Create();
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
