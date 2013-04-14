<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\DaySerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class DaysController extends AbstractRestfulController
{
	
	public function getList(){
		$dayRepo = $this->getServiceLocator()->get('ecamp.repo.day');
		$days = $dayRepo->findAll();
		
		$daySerializer = new DaySerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($daySerializer($days));
	}
	
	public function get($id){
		$dayRepo = $this->getServiceLocator()->get('ecamp.repo.day');
		$day = $dayRepo->find($id);
		
		$daySerializer = new DaySerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($daySerializer($day));
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
