<?php
namespace EcampApi\Resource\EventInstance;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\HalResource;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class EventInstanceResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\EventInstanceRepository
     */
    protected function getEventInstanceRepository(){
        return $this->getService('EcampCore\Repository\EventInstance');
    }

	/**
	 * @return \EcampCore\Service\EventInstanceService
	 */
	protected function getEventInstanceService(){
		return $this->getService('EcampCore\Service\EventInstance');
	}


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
        $this->listeners[] = $events->attach('delete', array($this, 'onDelete'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getEventInstanceRepository()->find($id);

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

        return $this->getEventInstanceRepository()->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->getEventInstanceRepository()->find($id);

        $data = array(
            'minOffsetStart' => (int) $data->start_min,
            'minOffsetEnd' => (int) $data->end_min,
            'leftOffset' => (double) $data->left,
            'width' => (double) $data->width
        );

        $this->getEventInstanceService()->Update($id, $data);

        return new EventInstanceDetailResource($entity);
    }

    public function onDelete(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        return $this->getEventInstanceService()->Delete($id);
    }
}
