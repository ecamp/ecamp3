<?php

class SchemaManager
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	
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
				
	}
	
	
	public function dropAllTables()
	{
		$this->rawDB->dropAllTables();
	}
	
	public function createSchema()
	{
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->createSchema($metadatas);
		
	}
	
	public function updateSchema()
	{
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->updateSchema($metadatas);
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
	}
	
	
}