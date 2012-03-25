<?php

class SchemaManager
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	/**
	 * @var Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	private $sm;
	
	
	private $dumpPath;
	
	
	
	public function __construct($em)
	{
		$this->em = $em;
		$this->sm = $this->em->getConnection()->getSchemaManager();
		
		$this->dumpPath = APPLICATION_PATH . "/../data/db/";
	}
	
	
	public function dropAllTables()
	{
		$tables = $this->sm->listTableNames();
		
		foreach($tables as $table)
		{
			$fks = $this->sm->listTableForeignKeys($table);
			
			foreach($fks as $fk)
			{
				$this->sm->dropForeignKey($fk, $table);
			}
		}
		
		foreach($tables as $table)
		{
			$this->sm->dropTable($table);
		}
	}
	
	
	public function createSchema()
	{
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->createSchema($metadatas);
		
	}
	
	
	public function clearAllTables()
	{
		//TODO: Use more clever code here!!
		
		$this->dropAllTables();
		$this->createSchema();
	}
	
	
	public function loadSqlDump($file)
	{
		return $this->runSqlFile($this->dumpPath . $file);
	}
	
	
	public function runSqlFile($file)
	{
		global $mysqlBinPath;
		
		$user = $this->em->getConnection()->getUsername();
		$pass = $this->em->getConnection()->getPassword();
		$db =	$this->em->getConnection()->getDatabase();
		
		
		$commands = array();
		
		$commands[] = 'PATH='.$mysqlBinPath.':$PATH;';
		$commands[] = 'DBUSER="' . $user . '";';
		$commands[] = 'DBPASS="' . $pass . '";';
		$commands[] = 'DB="' . $db . '";';
		$commands[] = 'FILE="' . $file . '";';
		
		$commands[] = 'source ' . APPLICATION_PATH . '/../bin/db/runSql.sh;';

		exec(implode(PHP_EOL, $commands), $ret);	return $ret;
	}
	
}