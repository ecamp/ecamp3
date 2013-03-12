<?php

class ApiApp_CampController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\CampRepository
	 * @Inject Core\Repository\CampRepository
	 */
	private $campRepo;
	
	
	public function init(){
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\Camp', 
			new ApiApp\Serializer\CampSerializer($this->getMime()));
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	$filter = $this->createFilter('creator', 'owner', 'group');
    	$camps = $this->campRepo->findBy($filter);
    	
    	$camps = $this->serialize($camps);
    	$this->setReturn($camps);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$camp = $this->campRepo->find($this->getId());
    	
    	$camp = $this->serialize($camp);
    	$this->setReturn($camps);
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