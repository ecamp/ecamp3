<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\UserSerializer;
use EcampCore\Repository\Provider\UserRepositoryProvider;
use EcampCore\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class UsersController extends AbstractRestfulBaseController
	implements UserRepositoryProvider
{
	public function getList(){
		$users = $this->ecampCore_UserRepo()->findAll();
		
		$userSerializer = new UserSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($userSerializer($users));
	}
	
	public function get($id){
		$user = $this->ecampCore_UserRepo()->find($id);
		
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
