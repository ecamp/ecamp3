<?php
namespace EcampApi\Resource\Membership;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class MembershipResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\GroupMembershipRepository
     */
    protected function getGroupMembershipRepository(){
        return $this->getService('EcampCore\Repository\GroupMembership');
    }


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->getGroupMembershipRepository()->find($id);

        if (!$entity) {
            throw new DomainException('Collaboration not found', 404);
        }

        return new MembershipDetailResource($id, $entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['user'] = $e->getRouteParam('user', $e->getQueryParam('user'));
        $params['group'] = $e->getRouteParam('group', $e->getQueryParam('group'));

        return $this->getGroupMembershipRepository()->getCollection($params);
    }
}
