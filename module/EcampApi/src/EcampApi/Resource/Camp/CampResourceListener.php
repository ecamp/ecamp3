<?php
namespace EcampApi\Resource\Camp;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class CampResourceListener extends AbstractListenerAggregate
{
    protected $campRepo;
    protected $campService;

    public function __construct($campRepo, $campService)
    {
        $this->campRepo = $campRepo;
        $this->campService = $campService;
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
        $camp = $this->campRepo->find($id);

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

        return $this->campRepo->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');
        $dataArray = (array) $data;

        $camp = $this->campService->Update($id, $dataArray);

        return new CampDetailResource($camp);
    }

}
