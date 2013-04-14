<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\UserSerializer;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class UsersController extends AbstractRestfulController
{
	public function getList(){
		$userRepo = $this->getServiceLocator()->get('ecamp.repo.user');
		$users = $userRepo->findAll();
		
		$userSerializer = new UserSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($userSerializer($users));
	}
	
	public function get($id){
		$userRepo = $this->getServiceLocator()->get('ecamp.repo.user');
		$user = $userRepo->find($id);
		
		$userSerializer = new UserSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($userSerializer($user));
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
