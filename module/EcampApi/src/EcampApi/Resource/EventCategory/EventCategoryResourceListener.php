<?php
namespace EcampApi\Resource\EventCategory;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class EventCategoryResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\EventCategoryRepository
     */
    protected function getEventCategoryRepository()
    {
        return $this->getService('EcampCore\Repository\EventCategory');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getEventCategoryRepository()->find($id);

        if (!$entity) {
            throw new DomainException('EventCategory not found', 404);
        }

        return new EventCategoryDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));

        return $this->getEventCategoryRepository()->getCollection($params);
    }
}
