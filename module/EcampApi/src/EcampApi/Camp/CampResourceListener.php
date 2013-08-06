<?php
namespace EcampApi\Camp;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface; 

class CampResourceListener extends AbstractListenerAggregate
{
    protected $campRepo;

    public function __construct(\EcampCore\Repository\CampRepository $campRepo)
    {
        $this->campRepo = $campRepo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $camp = $this->campRepo->find($id);
        
        if (!$camp) {
            throw new DomainException('Camp not found', 404);
        }
        
        return new CampDetailResource($camp);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	$params = $e->getQueryParams();
    	$params['user'] = $e->getRouteParam('user', $e->getQueryParam('user'));	
    	$e->setQueryParams($params);
    			
    	return $this->campRepo->getApiCollection($params->toArray());
    }
}