<?php

namespace RawDB; 

use RawDB\Adapter\IAdapter;

class RawDB
{
	private $config;
	
	/**
	 * @var RawDB\Adapter\BaseAdapter
	 */
	private $adapter;
	
	
	public function __construct($config = null)
	{
		$this->config = $config ?: new Config();
		
		$adapterName = "\RawDB\Adapter\\" . $this->config->os;
		$this->adapter = new $adapterName();
		
		$this->adapter->setConfig($this->config);
	}
	
	
	public function setLogin($user, $password)
	{
		$this->config->user = $user;
		$this->config->password = $password;
		
		return $this;
	}
	
	public function setDatabase($database)
	{
		$this->config->database = $database;
		
		return $this;
	}
	
	public function setBasePath($basePath = null)
	{
		if(is_null($basePath))
		{
			$this->config->basePath = "";
		}
		else
		{
			$basePath = rtrim($basePath, '\/');
			$this->config->basePath = $basePath . DIRECTORY_SEPARATOR;
		}
		
		return $this;
	}
	
	public function setMysqlPath($mysqlPath = null)
	{
		if(is_null($mysqlPath))
		{
			$this->config->mysqlPath = "";
		}
		else
		{
			$mysqlPath = rtrim($mysqlPath, '\/');
			$this->config->mysqlPath = $mysqlPath . DIRECTORY_SEPARATOR;
		}
		
		return $this;
	}

	
	public function runSqlFile($file)
	{
		$retval = array();
		
		exec('echo "runSqlFile(' . $file . ')"');
		
		$command = $this->adapter->getCommand_RunSqlFile($file);
		exec($command, $retval);
		
		return $retval;
	}
	
	
	public function dumpDatabase($file)
	{
		$retval = array();
		
		exec('echo "dumpDatabase(' . $file . ')"');
		
		$command = $this->adapter->getCommand_DumpDatabaseToFile($file);
		exec($command, $retval);
		
		return $retval;
	}
	
	
	public function dropAllTables()
	{
		$retval = array();
		
		exec('echo "dropAllTables()"');
		
		$command = $this->adapter->getCommand_DropAllTables();
		exec($command, $retval);
		
		return $retval;
	}
	
	
}