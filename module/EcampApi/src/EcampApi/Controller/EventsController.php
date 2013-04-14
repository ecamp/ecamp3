<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class EventsController extends AbstractRestfulController
{
	
	public function getList(){
		$eventRepo = $this->getServiceLocator()->get('ecamp.repo.event');
		$events = $eventRepo->findAll();
		
		$eventSerializer = new EventSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($eventSerializer($events));
	}
	
	public function get($id){
		$eventRepo = $this->getServiceLocator()->get('ecamp.repo.event');
		$event = $eventRepo->find($id);
		
		$eventSerializer = new EventSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($eventSerializer($event));
	}
	
	public function head($id = null){
        $format = $this->params('format');
		die("head." . $format);
	}
	
	public function create($data){
        $format = $this->params('format');
		die("create." . $format);
	}
	
	public function update($id, $data){
        $format = $this->params('format');
		die("update." . $format);
	}
	
	public function delete($id){
        $format = $this->params('format');
		die("delete." . $format);
	}
}
