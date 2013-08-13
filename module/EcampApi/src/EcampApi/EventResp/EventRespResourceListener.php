<?php
namespace EcampApi\EventResp;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface; 

class EventRespResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct(\EcampCore\Repository\EventRespRepository $repo)
    {
        $this->repo = $repo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->repo->find($id);
        
        if (!$entity) {
            throw new DomainException('EventResp not found', 404);
        }
        
        return new EventRespDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	$params = $e->getQueryParams()->toArray();
    	$params['event'] = $e->getRouteParam('event', $e->getQueryParam('event'));
    	$params['user'] = $e->getRouteParam('user', $e->getQueryParam('user'));
    	$params['collaboration'] = $e->getRouteParam('collaboration', $e->getQueryParam('collaboration'));
    	
    	return $this->repo->getCollection($params);
    }
}