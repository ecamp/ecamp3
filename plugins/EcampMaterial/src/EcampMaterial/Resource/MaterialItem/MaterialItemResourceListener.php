<?php
namespace EcampMaterial\Resource\MaterialItem;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class MaterialItemResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->repo->find($id);

        if (!$entity) {
            throw new DomainException('Event not found', 404);
        }

        return new MaterialItemDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['eventPlugin'] = $e->getRouteParam('eventPlugin', $e->getQueryParam('eventPlugin'));

        return $this->repo->getCollection($params);
    }
}
