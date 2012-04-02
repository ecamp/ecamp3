<?php

abstract class InstallerBase
{
	
	/**
	 * @var Zend_Config
	 */
	protected $config;
	
	public function __construct(Zend_Config $config)
	{
		$this->config = $config;
	}
	
	
	
	public abstract function IsInstalled();
	
	public abstract function Install();
	
	public abstract function RenderForm();
	
}