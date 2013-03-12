<?php

class ApiApp_UserController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\UserRepository
	 * @Inject Core\Repository\UserRepository
	 */
	private $userRepo;
	
	public function init()
	{
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\User',
			new ApiApp\Serializer\UserSerializer($this->getMime()));
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	$users = $this->userRepo->findAll();
    	$users = $this->serialize($users);
    	
    	$this->setReturn($users);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$user = $this->userRepo->find($this->getId());
    	$user = $this->serialize($user);
    	
    	$this->setReturn($user);
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

