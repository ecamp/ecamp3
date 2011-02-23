<?php

namespace Inject\Syntax;



interface IBindingRoot
{

	/**
	 * @abstract
	 * @return IBindingToSyntax
	 */
	public function Bind($className);

	/**
	 * @abstract
	 * @return IBindingToSyntax
	 */
	public function Unbind($className);

	/**
	 * @abstract
	 * @return IBindingToSyntax
	 */
	public function Rebinding($className);

}
