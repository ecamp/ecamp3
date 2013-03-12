<?php

class ApiApp_EventInstanceController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\EventInstanceRepository
	 * @Inject Core\Repository\EventInstanceRepository
	 */
	private $eventInstanceRepo;
	
	
	public function init(){
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\EventInstance', 
			new \ApiApp\Serializer\EventInstanceSerializer($this->getMime()));
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	
    	$filter = $this->createFilter('event', 'period');
    	$eventInstances = $this->eventInstanceRepo->findBy($filter);
    	
    	$eventInstances = $this->serialize($eventInstances);
    	$this->setReturn($eventInstances);
    }
    
    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$eventInstance = $this->eventInstanceRepo->find($this->getId());
    	
    	$eventInstance = $this->serialize($eventInstance);
    	$this->setReturn($eventInstance);
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