<?php

namespace Inject;

use \Inject\Syntax\IProvider;
use \Inject\Syntax\IAsSingleton;

use \Inject\Kernel\IKernel;
use \Inject\Kernel\IHaveKernel;

use \Inject\Binding\IBinding;
use \Inject\Binding\BindClass;
use \Inject\Binding\BindConstant;
use \Inject\Binding\BindToProvider;


class BindTo
{

	/**
	 * @var \Inject\Kernel\IKernel
	 */
	private $kernel;

	/**
	 * @var string
	 */
	private $className;


	public function __construct(IKernel $kernel, $className)
	{
		$this->kernel = $kernel;
		$this->className = $className;
	}


	/**
	 * @param string $className
	 * @return \Inject\Syntax\IAsSingleton
	 */
	public function To($className)
	{
		$binding = new BindClass($className);

		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @param object $object
	 * @return \Inject\Binding\IBinding
	 */
	public function ToConstant($object)
	{
		$binding = new BindConstant($this->className, $object);

		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @param Syntax\IProvider $provider
	 * @return \Inject\Syntax\IAsSingleton
	 */
	public function ToProvider(IProvider $provider)
	{
		$binding = new BindToProvider($this->className, $provider);

		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @return \Inject\Syntax\IAsSingleton
	 */
	public function ToSelf()
	{
		$binding = new BindClass($this->className);

		$this->kernel->SetBinding($binding);

		return $binding;
	}
}
