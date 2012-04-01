<?php

namespace RawDB\Adapter;

use RawDB\Config;

abstract class BaseAdapter
{
	/**
	 * @var RawDB\Config
	 */
	protected $config;
	
	
	function setConfig(Config $config)
	{
		$this->config = $config;
	}
	
	public abstract function getCommand_RunSqlFile($file);
	public abstract function getCommand_DumpDatabaseToFile($file);
	public abstract function getCommand_DropAllTables();
}
