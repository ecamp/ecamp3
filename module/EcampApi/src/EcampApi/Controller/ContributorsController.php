<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\ContributorSerializer;

use EcampApi\Serializer\UserSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class ContributorsController extends AbstractRestfulController
{
	public function getList(){
		$contributorRepo = $this->getServiceLocator()->get('ecamp.repo.contributor');
		$contributors = $contributorRepo->findAll();
		
		$contributorSerializer = new ContributorSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($contributorSerializer($contributors));
	}
	
	public function get($id){
		$contributorRepo = $this->getServiceLocator()->get('ecamp.repo.contributor');
		$contributor = $contributorRepo->find($id);
		
		$contributorSerializer = new ContributorSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($contributorSerializer($contributor));
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
