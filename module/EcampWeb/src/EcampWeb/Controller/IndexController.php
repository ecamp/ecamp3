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
		
		$camp = $this->getServiceLocator()->get('EcampCore\Service\Camp')->Get("2");
		
		$form = new \EcampCore\Form\CampUpdateForm($this->getServiceLocator());
		$form->bind($camp);
		
		if ($form->isValid()) {
			// Contains only the "name", "email"
			var_dump($form->getData());
		}
		
		var_dump( $form->getData(\Zend\Form\FormInterface::VALUES_AS_ARRAY));
		
		die();

		return new ViewModel();
	}

}