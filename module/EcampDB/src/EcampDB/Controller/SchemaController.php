<?php

namespace EcampDB\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class SchemaController extends AbstractActionController 
{
	
	public function indexAction(){
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function createAction(){
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
		
		$metadatas = $em->getMetadataFactory()->getAllMetadata();
		$schemaTool->createSchema($metadatas);
		
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function updateAction(){
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
		
		$metadatas = $em->getMetadataFactory()->getAllMetadata();
		$schemaTool->updateSchema($metadatas);
		
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function dropAction(){
		
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$schemaTool->dropDatabase();
		
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
}