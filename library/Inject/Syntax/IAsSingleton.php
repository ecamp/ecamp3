<?php

namespace Inject\Syntax;

interface IAsSingleton
{

	/**
	 * @abstract
	 * @return IAsSingleton
	 */
	public function AsSingleton();


	/**
	 * @abstract
	 * @return IAsSingleton
	 */
	public function GetAlwaysNewInstance();	
}
