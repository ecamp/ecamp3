<?php

namespace Inject;

use Inject\Factory\ByProvider;
use Inject\Factory\ByConstant;
use Inject\Factory\ByClassname;

use \Inject\Syntax\IProvider;
use \Inject\Syntax\IAsSingleton;

use \Inject\Kernel\IKernel;
use \Inject\Kernel\IHaveKernel;



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
		//$binding = new BindClass($className);
		
		$factory = new ByClassname($classname);
		$binding = new DependencyContainer($this->className, $factory);
		
		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @param object $object
	 * @return \Inject\Binding\IBinding
	 */
	public function ToConstant($object)
	{
		//$binding = new BindConstant($this->className, $object);

		$factory = new ByConstant($object);
		$binding = new DependencyContainer($this->className, $factory);
		$binding->AsSingleton(true);
		
		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @param Syntax\IProvider $provider
	 * @return \Inject\Syntax\IAsSingleton
	 */
	public function ToProvider(IProvider $provider)
	{
		//$binding = new BindToProvider($this->className, $provider);

		$factory = new ByProvider($provider);
		$binding = new DependencyContainer($this->className, $factory);
		
		$this->kernel->SetBinding($binding);

		return $binding;
	}


	/**
	 * @return \Inject\Syntax\IAsSingleton
	 */
	public function ToSelf()
	{
		//$binding = new BindClass($this->className);

		$factory = new ByClassname($this->className);
		$binding = new DependencyContainer($this->className, $factory);
		
		$this->kernel->SetBinding($binding);

		return $binding;
	}
}
