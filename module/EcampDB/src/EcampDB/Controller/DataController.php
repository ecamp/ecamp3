<?php

namespace EcampDB\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class DataController extends AbstractActionController 
{
	
	public function indexAction(){
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function importAction(){
		
		
		//new \Doctrine\DBAL\Tools\Console\Command\ImportCommand()
		
		$connection = $this->getServiceLocator()->get('doctrine.connection.orm_default');
		$entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
			'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
			'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($connection),
			'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
		));
		
		$validationCommand = $this->getServiceLocator()->get('doctrinetools.orm.validate-schema');
		$validationCommand->setHelperSet($helperSet);
		
		$input = new \Symfony\Component\Console\Input\StringInput("");
		$output = new \Symfony\Component\Console\Output\ConsoleOutput();
		$validationCommand->run($input, $output);
		
		$o = stream_get_contents($output->getStream());
		
		die(var_dump($input) . var_dump($o));
		
		$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function exportAction(){
		
	}
	
	
// 	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
// 	$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
	
// 	$metadatas = $em->getMetadataFactory()->getAllMetadata();
// 	$schemaTool->dropSchema($metadatas);
	
// 	$this->redirect()->toRoute('db', array('controller' => 'index', 'action' => 'index'));
	
	
}