<?php

class ApiApp_EventController extends ApiApp\Controller\BaseController
{
	
	/**
	 * @var Core\Repository\EventRepository
	 * @Inject Core\Repository\EventRepository
	 */
	private $eventRepo;
	
	
	public function init(){
		parent::init();
		
		$this->defineSerializer('CoreApi\Entity\Event', 
			new \ApiApp\Serializer\EventSerializer($this->getMime()));
	}
	
	
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction(){
    	
    	$filter = $this->createFilter('camp');
    	$events = $this->eventRepo->findBy($filter);
    	
    	$events = $this->serialize($events);
    	$this->setReturn($events);
    }
    

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction(){
    	$event = $this->eventRepo->find($this->getId());
    	
    	$event = $this->serialize($event);
    	$this->setReturn($event);
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