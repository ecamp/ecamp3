<?php

namespace Inject\Factory;


use Inject\Syntax\IFactory;
use Inject\Syntax\IProvider;

class ByProvider
	implements IFactory
{
	
	private $provider;
	
	
	public function __construct(IProvider $provider)
	{
		$this->provider = $provider;
	}
	
	public function Create()
	{
		return $this->provider->Create();
	}
}