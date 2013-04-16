<?php

namespace EcampCore\Controller;

use EcampCore\ServiceUtil\ServiceProviderWriter;
use EcampCore\RepositoryUtil\RepositoryProviderWriter;

class ServiceController extends AbstractBaseController 
{
	public function createProviderAction(){
		
		$serviceProviderWriter = new ServiceProviderWriter();
		$serviceProviderWriter->writeServiceProvider();
		
	}
	
	public function createRepoProviderAction(){
		$repoProviderWriter = new RepositoryProviderWriter();
		$repoProviderWriter->writeRepositoryProvider();
		
	}
	
}