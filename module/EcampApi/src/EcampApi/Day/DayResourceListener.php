<?php
namespace EcampApi\Day;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface; 

class DayResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct(\EcampCore\Repository\DayRepository $repo)
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
            throw new DomainException('Day not found', 404);
        }
        
        return new DayDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	$params = $e->getQueryParams()->toArray();
    	$params['period'] = $e->getRouteParam('period', $e->getQueryParam('period'));
    	
    	return $this->repo->getCollection($params);
    }
}