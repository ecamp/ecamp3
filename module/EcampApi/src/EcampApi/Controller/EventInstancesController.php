<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventInstanceSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class EventInstancesController extends AbstractRestfulController
{
	
	public function getList(){
		$eventInstanceRepo = $this->getServiceLocator()->get('ecamp.repo.eventinstance');
		$eventInstances = $eventInstanceRepo->findAll();
		
		$eventInstanceSerializer = new EventInstanceSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($eventInstanceSerializer($eventInstances));
	}
	
	public function get($id){
		$eventInstanceRepo = $this->getServiceLocator()->get('ecamp.repo.eventinstance');
		$eventInstance = $eventInstanceRepo->find($id);
				
		$eventInstanceSerializer = new EventInstanceSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($eventInstanceSerializer($eventInstance));
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
