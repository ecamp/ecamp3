<?php

namespace PhpDI\Syntax;

use PhpDI\Provider\Provider;


class AsSingleton
{
	
	private $provider;
	
	public function __construct(Provider $provider)
	{
		$this->provider = $provider;
	}
	
	public function AsSingleton()
	{
		$this->provider->asSingleton(true);
	}
	
}