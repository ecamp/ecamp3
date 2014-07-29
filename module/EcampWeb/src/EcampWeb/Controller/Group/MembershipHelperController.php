<?php

namespace EcampWeb\Controller\Group;

use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\User;
use Zend\View\Model\ViewModel;

class MembershipHelperController
    extends BaseController
{
    /**
     * @return \EcampCore\Service\GroupMembershipService
     */
    private function getGroupMembershipService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\GroupMembership');
    }

    public function requestMembershipAction()
    {
        $group = $this->getRouteGroup();
        $role = $this->params()->fromQuery('role', GroupMembership::ROLE_MEMBER);

        try {
            $this->getGroupMembershipService()->requestMembership($this->getMe(), $group, $role);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group);
    }

    public function revokeRequestAction()
    {
        $group = $this->getRouteGroup();

        try {
            $this->getGroupMembershipService()->revokeRequest($this->getMe(), $group);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group);
    }

    public function rejectRequestAction()
    {
        $group = $this->getRouteGroup();
        $user = $this->getQueryUser();

        try {
            $this->getGroupMembershipService()->rejectRequest($group, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group, $user);
    }

    public function acceptRequestAction()
    {
        $group = $this->getRouteGroup();
        $user = $this->getQueryUser();
        $role = $this->params()->fromQuery('role', GroupMembership::ROLE_MEMBER);

        try {
            $this->getGroupMembershipService()->acceptRequest($this->getMe(), $group, $user, $role);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group, $user);
    }

    public function inviteUserAction()
    {
        $group = $this->getRouteGroup();
        $user = $this->getQueryUser();
        $role = $this->params()->fromQuery('role', GroupMembership::ROLE_MEMBER);

        try {
            $this->getGroupMembershipService()->inviteUser($this->getMe(), $group, $user, $role);
        } catch (\Exception $ex) {
            var_dump($ex);
        }

        return $this->createViewModel($group, $user);
    }

    public function revokeInvitationAction()
    {
        $group = $this->getRouteGroup();
        $user = $this->getQueryUser();

        try {
            $this->getGroupMembershipService()->revokeInvitation($group, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group, $user);
    }

    public function rejectInvitationAction()
    {
        $group = $this->getRouteGroup();

        try {
            $this->getGroupMembershipService()->rejectInvitation($this->getMe(), $group);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group);
    }

    public function acceptInvitationAction()
    {
        $group = $this->getRouteGroup();

        try {
            $this->getGroupMembershipService()->acceptInvitation($this->getMe(), $group);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group);
    }

    public function leaveGroupAction()
    {
        $group = $this->getRouteGroup();

        try {
            $this->getGroupMembershipService()->leaveGroup($this->getMe(), $group);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group);
    }

    public function kickUserAction()
    {
        $group = $this->getRouteGroup();
        $user = $this->getQueryUser();

        try {
            $this->getGroupMembershipService()->kickUser($group, $user);
        } catch (\Exception $ex) {
        }

        return $this->createViewModel($group, $user);
    }

    private function createViewModel(Group $group, User $user = null)
    {
        $size = $this->params()->fromQuery('size', '');

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-web/helper/membership/membership.twig');
        $viewModel->setVariable('group', $group);
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('size', $size);

        return $viewModel;
    }

}
