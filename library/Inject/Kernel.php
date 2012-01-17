<?php

namespace Inject;

use \Inject\Kernel\IKernel;
use \Inject\Binding\IBinding;
use \Inject\Syntax\IBindingRoot;

class Kernel
	implements IKernel, IBindingRoot
{

	/**
	 * @var \ArrayObject
	 */
	private $bindings;

	/**
	 * @var \Inject\Injection\Injecter
	 */
	private $injecter;


	public function __construct()
	{
		$this->bindings = new \ArrayObject();

		$this->injecter = new Injection\Injecter($this);
	}

	/**
	 * @param string $className
	 * @return object
	 */
	public function Get($className)
	{
		if(array_key_exists($className, $this->bindings))
		{
			$object = $this->bindings[$className]->Get();
			
			if(! $this->bindings[$className]->IsDependencyInjectionRequired())
			{
				$this->injecter->InjectDependencies($object);
				$this->bindings[$className]->DependenciesInjected();
			}
		}
		else
		{
			if(!class_exists($className))
			{
				throw new \Exception("Class [" . $className . "] does not exist!");
			}

			$object = new $className();
			$this->injecter->InjectDependencies($object);
		}

		return $object;
	}

	/**
	 *
	 */
	public function InjectDependencies($object)
	{
		$this->injecter->InjectDependencies($object);
	}

	/**
	 * @param Binding\IBinding $binding
	 * @return void
	 */
	public function SetBinding(DependencyContainer $binding)
	{
		$this->bindings[$binding->ClassName()] = $binding;
	}

	/**
	 * @throws Exception
	 * @param  string $className
	 * @return BindTo
	 */
	public function Bind($className)
	{
		if(array_key_exists($className, $this->bindings))
		{
			throw new Exception("Use Rebind for already binded classes!");
		}

		return new BindTo($this, $className);
	}

	/**
	 * @param  string $className
	 * @return BindTo
	 */
	public function Rebinding($className)
	{
		return new BindTo($this, $className);
	}

	/**
	 * @param  string $className
	 * @return void
	 */
	public function Unbind($className)
	{
		if(array_key_exists($this->bindings, $className))
		{
			unset($this->bindings[$className]);
		}
	}
}
