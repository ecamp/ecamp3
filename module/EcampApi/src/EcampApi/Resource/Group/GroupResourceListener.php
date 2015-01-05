<?php
namespace EcampApi\Resource\Group;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class GroupResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->getService('EcampCore\Repository\Group');
    }

    /**
     * @return \EcampCore\Service\GroupService
     */
    protected function getGroupService()
    {
        return $this->getService('EcampCore\Service\Group');
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
        $group = $this->getGroupRepository()->find($id);

        if (!$group) {
            throw new DomainException('User not found', 404);
        }

        return new GroupDetailResource($group);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['group'] = $e->getRouteParam('group', $e->getQueryParam('group'));

        return $this->getGroupRepository()->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');
        $dataArray = (array) $data;

        $group = $this->getGroupService()->Update($id, $dataArray);

        return new GroupDetailResource($group);
    }

}
