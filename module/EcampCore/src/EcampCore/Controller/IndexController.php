<?php

namespace EcampCore\Controller;


use EcampCore\Repository\Provider\CampRepositoryProvider;
use EcampCore\Repository\Provider\GroupRepositoryProvider;
use EcampCore\Repository\Provider\UserRepositoryProvider;
use EcampCore\Service\Provider\UserServiceProvider;

use EcampCore\RepositoryUtil\RepositoryConfigWriter;
use EcampCore\RepositoryUtil\RepositoryProviderWriter;

use EcampCore\ServiceUtil\ServiceConfigWriter;
use EcampCore\ServiceUtil\ServiceProviderWriter;

class IndexController extends AbstractBaseController 
	implements 	UserRepositoryProvider
	,			UserServiceProvider
	,			GroupRepositoryProvider
	,			CampRepositoryProvider
{
	public function indexAction(){
		
		$groupId = $this->params('group');
		$campId = $this->params('camp');
		$userId = $this->params('user');

		if($groupId){
			$group = $this->ecampCore_GroupRepo()->find($groupId);
		}		
		
		if($campId){
			$camp = $this->ecampCore_CampRepo()->find($campId);
		}
		
		if($userId){
			$user = $this->ecampCore_UserRepo()->find($userId);
		}
		
		return array('group' => $group, 'camp' => $camp, 'user' => $user);
	}
	
	
	public function createServiceConfigAction(){
		$serviceConfigWriter = new ServiceConfigWriter($this->getServiceLocator());
		$serviceConfigWriter->writeServiceConfigs();
		
		return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function createServiceProvidersAction(){
		$serviceProviderWriter = new ServiceProviderWriter($this->getServiceLocator());
		$serviceProviderWriter->writeServiceProviderInterfaces();
		
		return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
	
	public function createRepoConfigAction(){
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$repoConfigWriter = new RepositoryConfigWriter($this->getServiceLocator(), $em);
		
		$repoConfigWriter->writeRepositoryConfigs();
		
		return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
	public function createRepoProvidersAction(){
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$repoProviderWriter = new RepositoryProviderWriter($this->getServiceLocator(), $em);
		
		$repoProviderWriter->writeRepositoryProviderInterfaces();
		
		return $this->redirect()->toRoute('core/default', array('controller' => 'index', 'action' => 'index'));
	}
	
	
	public function phpinfoAction(){
		phpinfo();
		die();
	}
}