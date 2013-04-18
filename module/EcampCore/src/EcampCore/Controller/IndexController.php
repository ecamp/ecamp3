<?php

namespace EcampCore\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use EcampCore\RepositoryUtil\RepositoryProviderWriter;
use EcampCore\ServiceUtil\ServiceProviderWriter;

class IndexController extends AbstractBaseController 
{
	
	public function indexAction(){}
	
	
	public function createServiceConfigAction(){
		$serviceProviderWriter = new ServiceProviderWriter();
		
		$serviceProviderWriter->writeServiceProvider();
		$serviceProviderWriter->writeServiceConfig();
		
		$this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function createRepoConfigAction(){
		$repoProviderWriter = new RepositoryProviderWriter();
		
		$repoProviderWriter->writeRepositoryProvider();
		$repoProviderWriter->writeRepositoryConfig();
		
		$this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
}