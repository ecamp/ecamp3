<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\CampSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class CampsController extends AbstractRestfulController
{
	
	public function getList(){
		$campRepo = $this->getServiceLocator()->get('ecamp.repo.camp');
		$camps = $campRepo->findAll();
		
		$campSerializer = new CampSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($campSerializer($camps));
	}
	
	public function get($id){
        $campRepo = $this->getServiceLocator()->get('ecamp.repo.camp');
		$camp = $campRepo->find($id);
		
		$campSerializer = new CampSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($campSerializer($camp));
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
