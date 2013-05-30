<?php

namespace EcampApi\Controller;


use EcampApi\Serializer\UserSerializer;
use EcampApi\Serializer\CampSerializer;

use EcampLib\Controller\AbstractRestfulBaseController;

use Zend\View\Model\JsonModel;

class IndexController extends AbstractRestfulBaseController
{
	
	/**
	 * @return EcampCore\Acl\ContextProvider
	 */
	private function contextProvider(){
		return $this->getServiceLocator()->get('ecamp.acl.contextprovider');
	}
	
	public function getList(){
		$userSerializer = new UserSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		$me = $this->me();
		$meRef = ($me != null) ? $userSerializer->getReference($me) : null;
		$camps = ($me != null) ? $this->ecampCore_CampRepo()->findUserCamps($me->getId()) : null;
		$friends = ($me != null) ? $this->ecampCore_UserRelationshipRepo()->findFriends($me) : null;
		
		return new JsonModel(array(
			'me' => $meRef,
			'camps' => $camps,
			'friends' => $friends
		));
	}
	
	public function get($id){
        $camp = $this->ecampCore_CampRepo()->find($id);
        
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
