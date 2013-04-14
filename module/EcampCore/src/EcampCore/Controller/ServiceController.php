<?php

namespace EcampCore\Controller;

use EcampCore\ServiceUtil\ServiceProviderWriter;

class ServiceController extends AbstractBaseController 
{
	public function createProviderAction(){
		
		$serviceProviderWriter = new ServiceProviderWriter();
		$serviceProviderWriter->writeServiceProvider();
		
	}
	
}