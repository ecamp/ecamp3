<?php

namespace PhpDI\Syntax;


use PhpDI\IKernel;
use PhpDI\Config;

use PhpDI\Factory\IFactory;
use PhpDI\Factory\Constant;
use PhpDI\Factory\Constructor;

use PhpDI\Provider\IProvider;
use PhpDI\Provider\Provider;


class Bind
{
	private $kernel;
	private $config;
	private $classname;
	
	public function __construct(IKernel $kernel, Config $config, $classname)
	{
		$this->kernel = $kernel;
		$this->config = $config;
		$this->classname = $classname;
	}
	
	
	/**
	 * @return PhpDI\Syntax\AsSingleton
	 */
	public function ToSelf()
	{
		$factory = new Constructor($this->classname);
		$provider = new Provider($factory, $this->kernel);
		
		$this->config[$this->classname] = $provider;
		
		return new AsSingleton($provider);
	}
	
	
	/**
	 * @return PhpDI\Syntax\AsSingleton
	 */
	public function To($classname)
	{
		$factory = new Constructor($classname);
		$provider = new Provider($factory, $this->kernel);
		
		$this->config[$this->classname] = $provider;	
		
		return new AsSingleton($provider);
	}
	
	
	/**
	 * @return PhpDI\Syntax\AsSingleton
	 */
	public function ToConstant($object)
	{
		$factory = new Constant($object);
		$provider = new Provider($factory, $this->kernel);
		
		$this->config[$this->classname] = $provider;
		
		return new AsSingleton($provider);
	}
	
	/**
	 * @return PhpDI\Syntax\AsSingleton
	 */
	public function ToFactory(IFactory $factory)
	{
		$provider = new Provider($factory, $this->kernel);
		$this->config[$this->classname] = $provider;
		
		return new AsSingleton($provider);
	}
	
	
	public function ToProvider(IProvider $provider)
	{
		$this->config[$this->classname] = $provider;
	}
}