<?php

namespace EcampCore\Controller;

use EcampCore\Render\EventRenderer;
use EcampCore\Repository\EventRepository;
use EcampLib\Controller\AbstractBaseController;

class EventController
	extends AbstractBaseController
{
	
	public function renderEventAction(){
		
		$eventRenderer = new EventRenderer();
		$eventRenderer->setServiceLocator($this->getServiceLocator());
		
		$event = $this->getServiceLocator()->get('EcampCore\Repository\Event')->find('ee1');
		$view = $eventRenderer->render($event);
				
		return $view;
	}
	
}