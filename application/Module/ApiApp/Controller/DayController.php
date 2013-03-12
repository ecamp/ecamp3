<?php

class ApiApp_DayController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\DayRepository
	 * @Inject Core\Repository\DayRepository
	 */
	private $dayRepo;
	
	public function init(){
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\Day', 
			new \ApiApp\Serializer\DaySerializer($this->getMime()));
		
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	$filter = $this->createFilter('period');
    	$days = $this->dayRepo->findBy($filter);
    	
    	$days = $this->serialize($days);
    	$this->setReturn($days);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$day = $this->dayRepo->find($this->getId());
    	
    	$day = $this->serialize($day);
    	$this->setReturn($day);
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction(){
    	
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction(){
    	
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction(){
    	
    }
	
}