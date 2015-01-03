<?php

namespace EcampMaterial\Resource\MaterialList;

use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class MaterialListResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();

        return $this->repo->findAll();
    }

}
