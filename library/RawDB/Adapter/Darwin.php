<?php

namespace RawDB\Adapter;

class Darwin
	extends BaseAdapter
{
	
	public function getCommand_RunSqlFile($file)
	{
		$basePath = $this->config->basePath;
		$file = $basePath . $file;
		
		$mysql = $this->config->mysqlPath . 'mysql';
		$user = $this->config->user;
		$password = $this->config->password;
		$database = $this->config->database;
		
		$commands = array();
		$commands[] = "if [ -a $file ]; then";
		$commands[] = "$mysql -u $user -p$password $database < $file";
		$commands[] = "fi";
		
		return implode(PHP_EOL, $commands);
	}
	
	public function getCommand_DumpDatabaseToFile($file)
	{
		$basePath = $this->config->basePath;
		$file = $basePath . $file;
		
		$mysqldump = $this->config->mysqlPath . 'mysqldump';
		$user = $this->config->user;
		$password = $this->config->password;
		$database = $this->config->database;
		
		$commands = array();
		$commands[] = "if [ ! -d $basePath ]; then";
		$commands[] = "mkdir -p $basePath";
		$commands[] = "fi";
		$commands[] = "$mysqldump -u $user -p$password --skip-dump-date --skip-comments $database > $file";
		
		return implode(PHP_EOL, $commands);
	}
	
	public function getCommand_DropAllTables()
	{
		$mysql = $this->config->mysqlPath . 'mysql';
		$user = $this->config->user;
		$password = $this->config->password;
		$database = $this->config->database;
		
		$commands = array();
		$commands[] = "$mysql -u $user -p$password $database -e \"show tables\"";
		$commands[] = "grep -v Tables_in";
		$commands[] = "grep -v \"+\"";
		$commands[] = "gawk '{print \"drop table \" $1 \";\"}'";
		$commands[] = "$mysql -u $user -p$password $database";
		
		return implode(" | ", $commands);
	}
}