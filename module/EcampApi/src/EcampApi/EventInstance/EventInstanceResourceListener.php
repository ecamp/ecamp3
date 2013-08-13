<?php
namespace EcampApi\EventInstance;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class EventInstanceResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct(\EcampCore\Repository\EventInstanceRepository $repo)
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
            throw new DomainException('EventInstance not found', 404);
        }

        return new EventInstanceDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));
        $params['period'] = $e->getRouteParam('period', $e->getQueryParam('period'));
        $params['day'] = $e->getRouteParam('day', $e->getQueryParam('day'));
        $params['event'] = $e->getRouteParam('event', $e->getQueryParam('event'));

        return $this->repo->getCollection($params);
    }
}
