<?php

namespace EcampWeb\Controller;

use Zend\View\Model\ViewModel;
use EcampCore\Service\CampService;

class IndexController
	extends BaseController
{

	public function indexAction(){
		
		/* @var $campRepo \EcampCore\Repository\CampRepository */
		$campRepo = $this->getServiceLocator()->get('EcampCore\Repository\Camp');
		var_dump( $campRepo->findOneBy(array())->getName() );
		
		var_dump( $this->getServiceLocator()->get('EcampCore\Service\Camp\Internal')->Get("2")->getName());
		
		/* @var $campService \EcampCore\Service\CampService */
		$campService = $this->getServiceLocator()->get('EcampCore\Service\Camp');
		
		$camp = $campService->Get("2");
		$campService->Delete($camp);
		
		die();

		return new ViewModel();
	}

}