<?php

namespace Inject\Kernel;

use Inject\DependencyContainer;

use \Inject\Binding\IBinding;

interface IKernel
{

	/**
	 * @abstract
	 * @param string $className
	 * @return object
	 */
	public function Get($className);


	/**
	 * @abstract
	 * @param \Binding\IBinding $binding
	 * @return void
	 */
	public function SetBinding(DependencyContainer $binding);

}
