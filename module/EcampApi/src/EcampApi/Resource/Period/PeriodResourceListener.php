<?php
namespace EcampApi\Resource\Period;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class PeriodResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\PeriodRepository
     */
    protected function getPeriodRepository()
    {
        return $this->getService('EcampCore\Repository\Period');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getPeriodRepository()->find($id);

        if (!$entity) {
            throw new DomainException('Period not found', 404);
        }

        return new PeriodDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));

        return $this->getPeriodRepository()->getCollection($params);
    }
}
