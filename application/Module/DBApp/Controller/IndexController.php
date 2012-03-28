<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

class DBApp_IndexController extends \Zend_Controller_Action
{
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	private $dumpPath;
	
	private $rawDB;
	
	
	public function init()
	{
		\Zend_Registry::get('kernel')->Inject($this);
		
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

	public function indexAction()
	{
		$files = scandir($this->dumpPath);
		$file = $this->getRequest()->getParam('file');
		$dumpname = $this->getRequest()->getParam('dumpname');
		
		$dumps = array_filter($files, 
			function($file)
			{
				$i = pathinfo($file);
				return array_key_exists('extension', $i) && $i['extension'] == 'sql';
			});
		
		
    	$this->view->dumps = $dumps;
    	$this->view->loadedFile = $file;
    	$this->view->dumpname = $dumpname;
    }
    
    
    
	public function loadAction()
	{
		$file = $this->getRequest()->getParam('file');
		
		$i = pathinfo($this->dumpPath . $file);
		if($i['extension'] != 'sql')
		{	$this->_forward('index');	return;	}
		
		
		$this->rawDB->dropAllTables();
		$this->rawDB->runSqlFile($file);
		
		$this->_redirect('/?file=' . $file);
	}
	
	
	public function loademptyAction()
	{
		$this->rawDB->dropAllTables();
		
		
		$metadatas = $this->em->getMetadataFactory()->getAllMetadata();
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$schemaTool->createSchema($metadatas);
		
		
		$this->_redirect('/');
	}
	
	
	public function saveAction()
	{
		$dumpname = $this->getRequest()->getParam('dumpname');
		if(strlen($dumpname) == 0)
		{	$this->_forward('index');	return;	}
		
		$file = $dumpname;
		
		$i = pathinfo($file);
		if(!array_key_exists('extension', $i) || $i['extension'] != 'sql')
		{	$file .= ".sql";	}
		
		$this->rawDB->dumpDatabase($file);
	
		$this->_redirect('/?dumpname=' . $dumpname);
	}
	
	
	public function deleteAction()
	{
		$dumpname = $this->getRequest()->getParam('dumpname');
		if(strlen($dumpname) == 0)
		{	$this->_forward('index');	return;	}
		
		$file = $this->dumpPath . $dumpname;
		
		$i = pathinfo($file);
		if($i['extension'] != 'sql')
		{	$this->_forward('index');	return;	}
		
		
		if(file_exists($file))
		{	unlink($file);	}
		
		$this->_redirect('/');
	}
		
}
