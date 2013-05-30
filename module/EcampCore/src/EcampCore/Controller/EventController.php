<?php

namespace EcampCore\Controller;

use EcampCore\Render\EventRenderer;
use EcampCore\Repository\EventRepository;
use EcampLib\Controller\AbstractBaseController;

class EventController
	extends AbstractBaseController
{
	
	/** @var EventRepository */
	private $eventRepo;
	
	public function __construct(EventRepository $eventRepo){
		$this->eventRepo = $eventRepo;
	}
	
	public function renderEventAction(){
		
		$eventRenderer = new EventRenderer();
		$eventRenderer->setServiceLocator($this->getServiceLocator());
		
		$event = $this->eventRepo->find('ee1');
		$view = $eventRenderer->render($event);
				
		return $view;
	}
	
}