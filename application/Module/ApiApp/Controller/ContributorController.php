<?php

class ApiApp_ContributorController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\UserCampRepository
	 * @Inject Core\Repository\UserCampRepository
	 */
	private $usercampRepo;
	
	/**
	 * @var Zend_Controller_Router_Interface
	 */
	private $router;
	
	
	public function init()
	{
		parent::init();
		
		$this->router = \Zend_Controller_Front::getInstance()->getRouter();
		
		$this->defineSerializer('CoreApi\Entity\UserCamp',
			new ApiApp\Serializer\ContributorSerializer($this->getMime()));
// 			function(CoreApi\Entity\UserCamp $uc){
// 				return array(
// 					'id'		=> 	$uc->getId(),
// 					'href'		=>	$this->getContributorHref($uc),
// 					'user_id'	=> 	$uc->getUser()->getId(),
// 					'user_href'	=>	$this->getUserHref($uc->getUser()),
// 					'camp_id'	=>	$uc->getCamp()->getId(),
// 					'role'		=>	$uc->getRole()
// 				);
// 			});
	}
	
	private function getContributorHref(CoreApi\Entity\UserCamp $userCamp){
		return 
			$this->router->assemble(
				array(
					'id' => $userCamp->getId(), 
					'mime' => $this->getRequest()->getParam('mime')
				), 
				'api.v1.contributor'
			);
	}
	
	private function getUserHref(CoreApi\Entity\User $user){
		return 
			$this->router->assemble(
				array(
					'id' => $user->getId(), 
					'mime' => $this->getMime()
				), 
				'api.v1.user'
			);
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	
    	$filter = $this->createFilter('user', 'camp');
    	$userCamps = $this->usercampRepo->findBy($filter);
    	$userCamps = $this->serialize($userCamps);
    	
    	$this->setReturn($userCamps);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$userCamp = $this->usercampRepo->find($this->getId());
    	$userCamp = $this->serialize($userCamp);
    	
    	$this->setReturn($userCamp);
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction(){
    	throw new Exception("Not implemented");
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction(){
    	throw new Exception("Not implemented");
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction(){
    	throw new Exception("Not implemented");
    }
	
}
