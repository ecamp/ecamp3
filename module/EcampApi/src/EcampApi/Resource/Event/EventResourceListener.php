<?php
namespace EcampApi\Resource\Event;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class EventResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\EventRepository
     */
    protected function getEventRepository(){
        return $this->getService('EcampCore\Repository\Event');
    }


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getEventRepository()->find($id);

        if (!$entity) {
            throw new DomainException('Event not found', 404);
        }

        return new EventDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));

        return $this->getEventRepository()->getCollection($params);
    }
}
