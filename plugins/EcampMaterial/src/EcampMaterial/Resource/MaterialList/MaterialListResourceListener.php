<?php

namespace EcampMaterial\Resource\MaterialList;

use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class MaterialListResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    protected $eventPluginRepo;

    public function __construct($repo, $eventPluginRepo)
    {
        $this->repo = $repo;
        $this->eventPluginRepo = $eventPluginRepo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPlugin', $e->getQueryParam('eventPlugin'));
        $eventPlugin = $this->eventPluginRepo->find($eventPluginId);

        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $eventPlugin->getCamp();

        return $this->repo->getCollection($params);
    }

}
