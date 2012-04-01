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
	
	private $rawDB;
	
	private $dumpPath;
	
	
	
	public function __construct($em)
	{
		$this->em = $em;
		$this->dumpPath = APPLICATION_PATH . "/../data/db/";
		
		$mysqlPath = null;
		if(file_exists(APPLICATION_PATH . '/../config.ini'))
		{
			$config	= new \Zend_Config_Ini(APPLICATION_PATH . '/../config.ini');
			$mysqlPath = isset($config->mysqlBinaryPath) ? $config->mysqlBinaryPath : null;
		}
		
		$user 		= $this->em->getConnection()->getUsername();
		$password 	= $this->em->getConnection()->getPassword();
		$database	= $this->em->getConnection()->getDatabase();
		
		$this->rawDB = new RawDB\RawDB();
		$this->rawDB->setBasePath($this->dumpPath);
		$this->rawDB->setLogin($user, $password);
		$this->rawDB->setDatabase($database);
		$this->rawDB->setMysqlPath($mysqlPath);
		
//		$this->sm = $this->em->getConnection()->getSchemaManager();
		
	}
	
	
	public function dropAllTables()
	{
		$this->rawDB->dropAllTables();
		
// 		$tables = $this->sm->listTableNames();
		
// 		foreach($tables as $table)
// 		{
// 			$fks = $this->sm->listTableForeignKeys($table);
			
// 			foreach($fks as $fk)
// 			{
// 				$this->sm->dropForeignKey($fk, $table);
// 			}
// 		}
		
// 		foreach($tables as $table)
// 		{
// 			$this->sm->dropTable($table);
// 		}
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
		return $this->rawDB->runSqlFile($file);
//		return $this->runSqlFile($this->dumpPath . $file);
	}
	
	
// 	public function runSqlFile($file)
// 	{
// 		global $mysqlBinPath;
		
// 		$user = $this->em->getConnection()->getUsername();
// 		$pass = $this->em->getConnection()->getPassword();
// 		$db =	$this->em->getConnection()->getDatabase();
		
// 		$commands = array();
		
// 		$commands[] = $mysqlBinPath."mysql -u $user -p$pass $db < $file";
		
// 		exec(implode(PHP_EOL, $commands), $ret);	
		
// 		return $ret;
// 	}
	
}