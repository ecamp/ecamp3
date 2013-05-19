<?php

namespace EcampCore\Controller;

use EcampCore\Render\EventRenderer;
use EcampCore\Repository\Provider\EventRepositoryProvider;

class EventController
	extends AbstractBaseController
	implements EventRepositoryProvider
{
	
	public function renderEventAction(){
		
		$eventRenderer = new EventRenderer();
		$eventRenderer->setServiceLocator($this->getServiceLocator());
		
		$event = $this->ecampCore_EventRepo()->find('ee1');
		$view = $eventRenderer->render($event);
		
		return $view;
	}
	
}