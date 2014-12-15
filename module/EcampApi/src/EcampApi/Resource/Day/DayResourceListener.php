<?php
namespace EcampApi\Resource\Day;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class DayResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\DayRepository
     */
    protected function getDayRepository(){
        return $this->getService('EcampCore\Repository\Day');
    }


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getDayRepository()->find($id);

        if (!$entity) {
            throw new DomainException('Day not found', 404);
        }

        return new DayDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['period'] = $e->getRouteParam('period', $e->getQueryParam('period'));

        return $this->getDayRepository()->getCollection($params);
    }
}
