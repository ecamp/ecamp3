<?php

namespace EcampApi\Controller;


use EcampApi\Serializer\UserSerializer;
use EcampApi\Serializer\ContributorSerializer;

use EcampCore\Controller\AbstractRestfulBaseController;
use EcampCore\Repository\Provider\ContributorRepositoryProvider;

use Zend\View\Model\JsonModel;

class ContributorsController extends AbstractRestfulBaseController
	implements ContributorRepositoryProvider
{
	public function getList(){
		$contributors = $this->ecampCore_UserCampRepo()->findAll();
		
		$contributorSerializer = new ContributorSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($contributorSerializer($contributors));
	}
	
	public function get($id){
		$contributor = $this->ecampCore_UserCampRepo()->find($id);
		
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
