<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\EventSerializer;
use EcampCore\Repository\Provider\EventRepositoryProvider;
use EcampCore\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class EventsController extends AbstractRestfulBaseController
	implements EventRepositoryProvider
{
	
	public function getList(){
		$events = $this->ecampCore_EventRepo()->findAll();
		
		$eventSerializer = new EventSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($eventSerializer($events));
	}
	
	public function get($id){
		$event = $this->ecampCore_EventRepo()->find($id);
		
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
