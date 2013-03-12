<?php

class ApiApp_PeriodController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\PeriodRepository
	 * @Inject Core\Repository\PeriodRepository
	 */
	private $periodRepo;
	
	
	public function init(){
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\Period', 
			new ApiApp\Serializer\PeriodSerializer($this->getMime()));
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	$filter = $this->createFilter('camp');
    	$periods = $this->periodRepo->findBy($filter);
    	
    	$periods = $this->serialize($periods);
    	$this->setReturn($periods);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$period = $this->periodRepo->find($this->getId());
    	
    	$period = $this->serialize($period);
    	$this->setReturn($period);
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