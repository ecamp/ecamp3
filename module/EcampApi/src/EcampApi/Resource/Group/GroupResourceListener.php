<?php
namespace EcampApi\Resource\Group;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class GroupResourceListener extends AbstractListenerAggregate
{
    protected $groupRepo;
    protected $groupService;

    public function __construct($groupRepo, $groupService)
    {
        $this->groupRepo = $groupRepo;
        $this->groupService = $groupService;
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
        $group = $this->repo->find($id);

        if (!$group) {
            throw new DomainException('User not found', 404);
        }

        return new GroupDetailResource($group);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['group'] = $e->getRouteParam('group', $e->getQueryParam('group'));

        return $this->repo->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');
        $dataArray = (array) $data;

        $group = $this->groupService->Update($id, $dataArray);

        return new GroupDetailResource($group);
    }

}
