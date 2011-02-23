<?php

namespace Inject\Kernel;


interface IHaveKernel
{
	/**
	 * @abstract
	 * @return IKernel
	 */
	function GetKernel();
}
