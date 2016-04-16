<?php
namespace EcampApi\Resource\Camp;

use EcampCore\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class CampResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\CampRepository
     */
    private function getCampRepository()
    {
        return $this->getService('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Service\CampService
     */
    private function getCampService()
    {
        return $this->getService('EcampCore\Service\Camp');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));

        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $camp = $this->getCampRepository()->find($id);

        if (!$camp) {
            throw new DomainException('Camp not found', 404);
        }

        return new CampDetailResource($camp);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['user'] = $e->getRouteParam('user', $e->getQueryParam('user'));
        $params['group'] = $e->getRouteParam('group', $e->getQueryParam('group'));

        return $this->getCampRepository()->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');
        $dataArray = (array) $data;

        $camp = $this->getCampService()->Update($id, $dataArray);

        return new CampDetailResource($camp);
    }

}
