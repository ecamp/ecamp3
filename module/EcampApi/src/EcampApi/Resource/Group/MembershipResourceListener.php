<?php
namespace EcampApi\Resource\Group;

use EcampApi\Resource\Membership\MembershipDetailResource;
use EcampCore\Resource\BaseResourceListener;
use PhlyRestfully\ResourceEvent;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;

class MembershipResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository()
    {
        return $this->getService('EcampCore\Repository\Group');
    }

    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\GroupMembershipRepository
     */
    protected function getGroupMembershipRepository()
    {
        return $this->getService('EcampCore\Repository\GroupMembership');
    }

    protected function getAuthenticationService()
    {
        return new AuthenticationService();
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));

    }

    public function onFetch(ResourceEvent $e)
    {
        $group = $this->getGroup($e);
        $user = $this->getUser($e);

        $groupMembership =
            $this->getGroupMembershipRepository()->findByGroupAndUser($group, $user);

        $membershipResource =
            new MembershipDetailResource($user->getId(), $groupMembership, $group, $user);

        $identifiedUser = $this->getIdentifiedUser();
        if ($identifiedUser != null) {
            $membershipResource->setVisitor($identifiedUser);
        }

        return $membershipResource;
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['user'] = $this->getUser($e);
        $params['group'] = $this->getGroup($e);

        return $this->getGroupMembershipRepository()->getCollection($params);
    }

    /**
     * @param  ResourceEvent           $e
     * @return \EcampCore\Entity\Group
     */
    protected function getGroup(ResourceEvent $e)
    {
        $groupId = $e->getRouteParam('group', $e->getQueryParam('group'));
        $group = ($groupId != null) ? $this->getGroupRepository()->find($groupId) : null;

        return $group;
    }

    /**
     * @param  ResourceEvent          $e
     * @return \EcampCore\Entity\User
     */
    protected function getUser(ResourceEvent $e)
    {
        $userId = $e->getRouteParam('member', $e->getQueryParam('member'));
        $user = ($userId != null) ? $this->getUserRepository()->find($userId) : null;

        return $user;
    }

}
