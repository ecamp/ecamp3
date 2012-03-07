<?php

namespace Inject\Factory;

use Inject\Syntax\IFactory;

class ByConstant
	implements IFactory
{
	
	private $const;
	
	
	public function __construct($const)
	{
		$this->const = $const;
	}
	
	public function Create()
	{
		return $this->const;
	}
}