<?php
namespace EcampApi\EventCategory;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface; 

class EventCategoryResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct(\EcampCore\Repository\EventCategoryRepository $repo)
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
            throw new DomainException('EventCategory not found', 404);
        }
        
        return new EventCategoryDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	$params = $e->getQueryParams()->toArray();
    	$params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));
    	
    	return $this->repo->getCollection($params);
    }
}