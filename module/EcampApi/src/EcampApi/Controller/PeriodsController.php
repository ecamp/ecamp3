<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\PeriodSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class PeriodsController extends AbstractRestfulController
{
	
	public function getList(){
		$periodRepo = $this->getServiceLocator()->get('ecamp.repo.period');
		$periods = $periodRepo->findAll();
		
		$periodSerializer = new PeriodSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($periodSerializer($periods));
	}
	
	public function get($id){
		$periodRepo = $this->getServiceLocator()->get('ecamp.repo.period');
		$period = $periodRepo->find($id);
		
		$periodSerializer = new PeriodSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($periodSerializer($period));
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
