<?php

namespace Inject\Kernel;

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
	public function SetBinding(IBinding $binding);

}
