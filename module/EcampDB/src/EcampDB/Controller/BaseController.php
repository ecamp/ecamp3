<?php

namespace EcampDB\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class BaseController extends AbstractActionController
{
    
    public function onDispatch(MvcEvent $e)
    {
       // Disable translation (in case for non-existing translation tables)  	
   
    	$translateHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Translate');
    	$translateHelper->setTranslatorTextDomain('default');
    	$translateHelper->setTranslatorEnabled(false);
    	
        parent::onDispatch($e);
    }

}
