<?php

namespace RawDB\Adapter;

class Linux
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
		$commands[] = "if [[ -a $file ]]; then";
		$commands[] = "$mysql -u $user $database < $file";
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
		$commands[] = "if [[ ! -d $basePath ]]; then";
		$commands[] = "mkdir -p $basePath";
		$commands[] = "fi";
		$commands[] = "$mysqldump -u $user --skip-dump-date --skip-comments $database > $file";
		
		return implode(PHP_EOL, $commands);
	}
	
	public function getCommand_DropAllTables()
	{
		$mysql = $this->config->mysqlPath . 'mysql';
		$user = $this->config->user;
		$password = $this->config->password;
		$database = $this->config->database;
		
		
		$drop = array();
		$drop[] = "$mysql -u $user $database -e \"show tables\"";
		$drop[] = "grep -v Tables_in";
		$drop[] = "grep -v \"+\"";
		$drop[] = "sed 's/^/drop table /g'";
		$drop[] = "sed 's/\$/;/'";
		$drop[] = "sed '1s/^/SET foreign_key_checks = 0; \\" . PHP_EOL . "/g'";
		
		//$drop[] = "gawk '{print \"drop table \" $1 \";\"}'";
		//$drop[] = "sed 's/^/SET foreign_key_checks = 0; /g'";
		//$drop[] = "(echo \"SET foreign_key_checks = 0;\"; gawk '{print \"drop table \" $1 \";\"}')";
		//$drop[] = "gawk '{print \"drop table \" $1 \";\"}'";
		$drop[] = "$mysql -u $user $database";
		
		return implode(" | ", $drop);
	}
}