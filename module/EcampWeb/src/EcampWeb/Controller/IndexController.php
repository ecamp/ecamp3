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
		
		var_dump( $this->getServiceLocator()->get('EcampCore\Service\Camp\Internal')->Get("1")->getName());
		
		var_dump( $this->getServiceLocator()->get('EcampCore\Service\Camp')->Get("1")->getName());
		
		die();

		return new ViewModel();
	}

}