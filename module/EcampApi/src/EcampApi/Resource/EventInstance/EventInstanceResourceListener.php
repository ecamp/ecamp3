<?php
namespace EcampApi\Resource\EventInstance;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class EventInstanceResourceListener extends AbstractListenerAggregate
{

    /**
     * @var \EcampCore\Repository\EventInstanceRepository
     */
    protected $repo;

    /**
     * @var \EcampCore\Service\EventInstanceService
     */
    protected $service;

    public function __construct($repo, $service)
    {
        $this->repo = $repo;
        $this->service = $service;
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
        $entity = $this->repo->find($id);

        if (!$entity) {
            throw new DomainException('EventInstance not found', 404);
        }

        return new EventInstanceDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));
        $params['period'] = $e->getRouteParam('period', $e->getQueryParam('period'));
        $params['day'] = $e->getRouteParam('day', $e->getQueryParam('day'));
        $params['event'] = $e->getRouteParam('event', $e->getQueryParam('event'));

        return $this->repo->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->repo->find($id);

        $data = array(
            'minOffsetStart' => (int) $data->start_min,
            'minOffsetEnd' => (int) $data->end_min,
            'leftOffset' => (double) $data->left,
            'width' => (double) $data->width
        );

        $this->service->Update($id, $data);

        return new EventInstanceDetailResource($entity);
    }
}
