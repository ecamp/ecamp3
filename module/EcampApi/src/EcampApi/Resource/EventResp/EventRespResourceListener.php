<?php
namespace EcampApi\Resource\EventResp;

use EcampCore\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class EventRespResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\EventRespRepository
     */
    protected function getEventRespRepository()
    {
        return $this->getService('EcampCore\Repository\EventResp');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getEventRespRepository()->find($id);

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

        return $this->getEventRespRepository()->getCollection($params);
    }
}
