<?php
namespace EcampApi\Resource\Collaboration;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class CollaborationResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\CampCollaborationRepository
     */
    protected function getCampCollaborationRepository(){
        return $this->getService('EcampCore\Repository\CampCollaboration');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getCampCollaborationRepository()->find($id);

        if (!$entity) {
            throw new DomainException('Collaboration not found', 404);
        }

        return new CollaborationDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['user'] = $e->getRouteParam('user', $e->getQueryParam('user'));
        $params['camp'] = $e->getRouteParam('camp', $e->getQueryParam('camp'));

        return $this->getCampCollaborationRepository()->getCollection($params);
    }
}
